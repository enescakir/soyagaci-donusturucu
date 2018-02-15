<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use App\Notifications\TreeReadyNotification;
use App\Enums\TreeStatus;
use App\Enums\TreeType;
use App\Enums\Relation;
use PHPHtmlParser\Dom;
use File;

class Tree 
{
    use Notifiable;

    public $status;
    public $name;
    public $slug;
    public $email;
    public $root;
    public $people;
    public $type;

    public function __construct()
    {
        $this->people = collect();
    }


    public function addPerson(Person $person)
    {
        if ($person->level == 0) {
            $this->root = $person;
            $this->name = "{$person->first_name} {$person->last_name} Adlı Kişinin Soyağacı";
        }
        $this->people->push($person);
    }

    public function getPath()
    {
        return route('tree.show', $this->slug);
    }

    public function setSlug()
    {
        $this->slug = str_random(5);
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function parsePDF($file)
    {
        $file->storeAs('inputs', "{$this->slug}.pdf");
        $this->setStatus(TreeStatus::PARSING);
        $this->convertPDF();
        $this->parsePdfData();
        $this->setupRelations();
        return $this;
    }

    public function parseHtml($file)
    {
        $file->storeAs('inputs', "{$this->slug}.html");
        $this->setStatus(TreeStatus::PARSING);
        $this->parseHtmlData();
        $this->setupRelations();
        return $this;
    }

    public function convertPDF()
    {
        $input_file = storage_path('app/inputs') . "/{$this->slug}.pdf";
        $output_file = storage_path('app/inputs') . "/{$this->slug}.html";
        $destination_file = storage_path('app/outputs') . "/{$this->slug}.html";
        $pdftohtml = config('app.pdftohtml');
        $command = "{$pdftohtml} -enc UTF-8 -i -noframes -c {$input_file} && mv {$output_file} {$destination_file}";
        $result = exec($command);
        if (!file_exists($destination_file)) {
            $this->setStatus(TreeStatus::CONVERT_ERROR);
            throw new \Exception('HTML dosyası bulunamadı');
        }
        return $this;
    }

    public function getRawPdfData()
    {
        $destination_file = storage_path('app/outputs') . "/{$this->slug}.html";
        if (!file_exists($destination_file)) {
            $this->setStatus(TreeStatus::CONVERT_ERROR);
            throw new \Exception('HTML dosyası bulunamadı');
        } else {
            $output = strip_tags(html_entity_decode(file_get_contents($destination_file), ENT_QUOTES, 'UTF-8'));
            $output = preg_replace('/\xc2\xa0/', ' ', $output);
            if (preg_match('/(ALT.+ÜST.+SOY.+BELGESİ)/', $output)) {
                $output = preg_replace("/^\n+|^[\t\s]*\n+/m", null, $output);
                $output = preg_replace('/^.+\n/', null, $output);
                $output = preg_replace('/^.*(DURUMU)\n/s', null, $output);
                $output = preg_replace('/(AÇIKLAMALAR).*$/s', null, $output);
                $output = preg_replace('/.*(T\.C).*\n/', null, $output);
                $output = preg_replace('/.*(İÇİŞLERİ).*\n/', null, $output);
                $output = preg_replace('/.*(NÜFUS).*\n/', null, $output);
                $output = preg_replace('/.*(BELGESİ).*\n/', null, $output);
                $output = preg_replace('/\d\s\/\s\d\n/', null, $output);
                return $output;
            } else {
                $this->setStatus(TreeStatus::CONTENT_ERROR);
                throw new \Exception('Geçersiz PDF dosyası');
                return false;
            }
        }
    }

    public function parsePdfData()
    {
        $lines = explode("\n", $this->getRawPdfData());
        $carry = collect();
        foreach ($lines as $line) {
            if (is_numeric($line)) {
                if ($carry->isNotEmpty()) {
                    $this->addPerson(Person::parseFromPdf($carry));
                }
                $carry = collect([$line]);
            } else {
                $carry->push($line);
            }
        }
        if ($carry->isNotEmpty()) {
            $this->addPerson(Person::parseFromPdf($carry));
        }
        return $this;
    }

    public function parseHtmlData()
    {
        $input_file = storage_path('app/inputs') . "/{$this->slug}.html";
        if (!file_exists($input_file)) {
            $this->setStatus(TreeStatus::CONVERT_ERROR);
            throw new \Exception('HTML dosyası bulunamadı');
        }
        $this->setStatus(TreeStatus::PARSING);
        $dom = new Dom;
        $dom->loadFromFile($input_file);
        if (preg_match('/(ALT.+ÜST.+SOY.+BELGESİ)/', $dom->innerHtml)) {
            $rows = $dom->find('.resultTable > tbody > tr');
            foreach ($rows as $row) {
                $columns = $row->find('td');
                if ($columns->count() == 12) {
                    $carry = collect();
                    foreach ($columns as $column) {
                        $carry->push($column->innerHtml);
                    }
                    $this->addPerson(Person::parseFromHtml($carry));
                }
            }
            return $this;
        } else {
            $this->setStatus(TreeStatus::CONTENT_ERROR);
            throw new \Exception('Geçersiz HTML dosyası');
            return false;
        }
    }

    public function setupRelations()
    {
        $people = $this->people->sortBy('level');
        foreach ($people as $person) {
            $current = $this->root;
            if ($person->level > 0) {
                $i = count($person->getRelations());
                foreach ($person->getRelations() as $relation) {
                    if ($i > 1) {
                        if (Relation::isMale($relation)) {
                            $current = $current->father;
                        } else {
                            $current = $current->mother;
                        }
                    } else {
                        if (Relation::isMale($relation)) {
                            $current->father = $person;
                        } else {
                            $current->mother = $person;
                        }
                    }
                    $i--;
                }
            }
        }
        foreach ($people as $person) {
            if (!$person->father && $person->father_name) {
                $person->father = new Person();
                $person->father->first_name = $person->father_name;
                $person->father->gender = 'Erkek';
                $person->father->level = $person->level + 1;
                $person->father->relation = $person->relation . ($person->gender == 'Kadın' ? 'nin' : 'nın') . ' Babası';
            }
            if (!$person->mother && $person->mother_name) {
                $person->mother = new Person();
                $person->mother->first_name = $person->mother_name;
                $person->mother->gender = 'Kadın';
                $person->mother->level = $person->level + 1;
                $person->mother->relation = $person->relation . ($person->gender == 'Kadın' ? 'nin' : 'nın') . ' Annesi';
            }
        }
        $this->setStatus(TreeStatus::SUCCESS);
        return $this;
    }

    public function json()
    {

        return [
            'status' => $this->status,
            'name'   => $this->name,
            'slug'   => $this->slug,
            'email'  => $this->email,
            'type'   => $this->type,
            'path'   => $this->getPath(),
            'people' => $this->root->json()
        ];
    }

    public function sendReadyNotification()
    {
        $this->notify(new TreeReadyNotification());
    }

}
