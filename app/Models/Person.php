<?php

namespace App\Models;

use Illuminate\Support\Collection;
use App\Enums\Relation;

class Person
{
    public $children;
    public $father;
    public $mother;

    public $id;
    public $first_name;
    public $last_name;
    public $father_name;
    public $mother_name;
    public $relation;
    public $level;
    public $gender;
    public $birth_place;
    public $birth_at;
    public $city;
    public $district;
    public $hometown;
    public $person_no;
    public $death_at;
    public $marrige_status;
    public $status;


    public static function parseFromHtml(Collection $parts)
    {
        $person = new static();
        $person->id = $parts->shift();
        $gender = $parts->shift();
        $person->gender = ($gender == 'K') ? 'Kadın' : (($gender == 'E') ? 'Erkek' : null);
        $person->relation = trim($parts->shift());
        $person->level = Relation::calculateLevel($person->relation);
        $person->first_name = $person->checkAndTrimString($parts->shift());
        $person->last_name = $person->checkAndTrimString($parts->shift());
        $person->father_name = $person->checkAndTrimString($parts->shift());
        $person->mother_name = $person->checkAndTrimString($parts->shift());
        $birth = $person->checkAndTrimString($parts->shift());
        if ($birth) {
            $birth = preg_split("/<\W*(b|B)r\W*>/", $birth);
            $person->birth_place = isset($birth[0]) ? $person->checkAndTrimString($birth[0]) : null ;
            $person->birth_at = isset($birth[1]) ? $person->checkAndTrimString($birth[1]) : null ;
        }
        $place = $person->checkAndTrimString($parts->shift());
        if ($place) {
            $place = preg_split("/<\W*(b|B)r\W*>/", $place);
            $person->city = isset($place[0]) ? $person->checkAndTrimString($place[0]) : null ;
            $person->district = isset($place[1]) ? $person->checkAndTrimString($place[1]) : null ;
            $person->hometown = isset($place[2]) ? $person->checkAndTrimString($place[2]) : null ;
        }
        $person->person_no = $person->checkAndTrimString($parts->shift());
        $person->marrige_status = $person->checkAndTrimString($parts->shift());
        $status = $person->checkAndTrimString($parts->shift());
        if ($status) {
            $status = preg_split("/<\W*(b|B)r\W*>/", $status);
            $person->status = isset($status[0]) ? $person->checkAndTrimString($status[0]) : null ;
            $person->death_at = isset($status[1]) ? $person->checkAndTrimString($status[1]) : null ;
        }
        return $person;
    }

    public static function parseFromPdf(Collection $parts)
    {
        $person = new static();
        $person->id = $parts->shift(); // [ID]
        $gender = $parts->shift(); // [Gender]
        $person->gender = ($gender == 'K') ? 'Kadın' : (($gender == 'E') ? 'Erkek' : null);

        $person->relation = trim($parts->shift()); // [Relation]
        while ($person->checkString($parts->first()) && !$person->checkUpper($parts->first())) {
            $person->relation .= ' ' . trim($parts->shift()); // [Relation]
        }
        $person->level = Relation::calculateLevel($person->relation);
        $current = $parts->pop(); // [Birth date] or [Death date]
        $next = $parts->pop(); // [Birth date] or [Status]
        if ($person->checkDate($next)) {
            $person->birth_at = $next;
            $person->death_at = $person->checkDate($current) ? $current : null;
            $next = $parts->pop(); // [Status]
            $person->status = $person->checkAndTrimString($next);
        } else {
            $person->birth_at = $person->checkDate($current) ? $current : null;
            $person->status = $person->checkAndTrimString($next);
        }
        $marrige = $parts->pop();
        $person->marrige_status = $person->checkAndTrimString($marrige);

        $person_no = $parts->pop();
        $person->person_no = $person->checkAndTrimString($person_no);

        $hometown = $parts->pop();
        if ($person->checkUpper($hometown)) {
            $person->hometown = $person->checkAndTrimString($hometown);
            while ($person->checkString($parts->last()) && $person->checkUpper($parts->last())) {
                $person->hometown = $parts->pop() . ' ' . $person->hometown; // [Other parts of hometown]
            }
            $district = $parts->pop();
            $person->district = $person->checkAndTrimString($district);
        } else {
            if (preg_match('/^(.*)\/(.*)$/', $hometown, $out)) {
                $person->hometown = isset($out[1]) ? $person->checkAndTrimString($out[1]) : null;
                $person->district = isset($out[2]) ? $person->checkAndTrimString($out[2]) : null;
            }
        }

        if ($person->checkString($parts->last()) && $person->checkUpper($parts->last())) {
            if (preg_match('/^(.*)\/(.*)$/', $person->district, $out)) {
                $person->city = isset($out[1]) ? $person->checkAndTrimString($out[1]) : null;
                $person->district = isset($out[2]) ? $person->checkAndTrimString($out[2]) : null;
            }
            $birth_place = $parts->pop();
            $person->birth_place = $person->checkAndTrimString($birth_place);
        } else {
            $city = $parts->pop();
            $person->city = $person->checkAndTrimString($city);

            $birth_place = $parts->pop();
            $person->birth_place = $person->checkAndTrimString($birth_place);
        }

        $mother_name = $parts->pop();
        $person->mother_name = $person->checkAndTrimString($mother_name);

        $father_name = $parts->pop();
        $person->father_name = $person->checkAndTrimString($father_name);

        $last_name = $parts->pop();
        $person->last_name = $person->checkAndTrimString($last_name);

        $person->first_name = $person->checkAndTrimString($parts->shift()); // [First Name]
        while ($parts->isNotEmpty() && $person->checkString($parts->first())) {
            $person->first_name .= ' ' . $person->checkAndTrimString($parts->shift());
        }
        return $person;
    }

    public function getRelations()
    {
        return explode(' ', $this->relation);
    }

    private function checkString($string)
    {
        return '-' != $string;
    }

    private function checkAndTrimString($string)
    {
        return $this->checkString($string) ? title_case_turkish(trim(trim($string, "/"))) : null;
    }

    private function checkDate($string)
    {
        return preg_match("/\d+[.]\d+[.]\d+/", $string);
    }

    private function checkUpper($string)
    {
        return mb_strtoupper($string, 'utf-8') == $string;
    }

    public function json()
    {
        $parents = [];
        if ($this->father) {
            array_push($parents, $this->father->json());
        }
        if ($this->mother) {
            array_push($parents, $this->mother->json());
        }
        return [
            'id'              => $this->id,
            'level'           => $this->level,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'gender'          => $this->gender,
            'relation'        => $this->relation,
            'father_name'     => $this->father_name,
            'mother_name'     => $this->mother_name,
            'birth_place'     => $this->birth_place,
            'birth_at'        => $this->birth_at,
            'city'            => $this->city,
            'district'        => $this->district,
            'hometown'        => $this->hometown,
            'death_at'        => $this->death_at,
            'marriage_status' => $this->marrige_status,
            'status'          => $this->status,
            'parents'         => $parents,
        ];
    }
}
