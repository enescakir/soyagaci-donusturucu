var margin = {
    top: 300,
    right: 200,
    bottom: 300,
    left: 200
};
var treeSize = {
    width: 1000,
    height: 500
};
var boxSize = {
    width: 150,
    height: 40
};
var nodeSize = {
    width: 100,
    height: 200
};

// Setup zoom and pan
var zoom = d3.zoom()
    .scaleExtent([.5, 2])
    .on('zoom', function () {
        svg.attr("transform", d3.event.transform)
    });
//zoom.translate([150, 200]);

var ftree = d3.tree()
    .nodeSize([nodeSize.width, nodeSize.height])
    .separation(function () {
        return .5;
    })
var root = ftree(d3.hierarchy(data, function (d) {
    return d.parents;
}))
var nodes = root.descendants()
var links = root.descendants().slice(1);
var svg = d3.select("#tree").append("svg")
    //.attr("width", treeSize.width)
    //.attr("height", treeSize.height)
    .append('g');
// Zoom
d3.select("svg").call(zoom)
// Change origin for initial zoom
d3.select("svg").call(zoom.transform, d3.zoomIdentity.translate(margin.left, margin.top))

// Style links (edges)
svg.selectAll("path.link")
    .data(links)
    .enter().append("path")
    .attr("class", "link")
    .attr("d", elbow);

// Style nodes    
var node = svg.selectAll("g.person")
    .data(nodes)
    .enter().append("g")
    .attr("class", function (d) {
        return "person" + " person-" + d.data.id +
            (d.data.gender == "Erkek" ? " male" : " female");
    })
    .attr("id", function (d) {
        return d.data.level == 0 ? "root" : null;
    })
    .attr("transform", function (d) {
        return "translate(" + d.y + "," + d.x + ")";
    });

// Draw the rectangle person boxes
node.append("rect")
    .attr("x", -(boxSize.width / 2))
    .attr("y", -(boxSize.height / 2))
    .attr("width", boxSize.width)
    .attr("height", boxSize.height);

// Draw the person's name and position it inside the box
node.append("text")
    .attr("dx", 0)
    .attr("dy", "0.3em")
    .attr("text-anchor", "middle")
    .attr('class', 'name')
    .text(function (d) {
        return d.data.last_name ? (d.data.first_name + " " + d.data.last_name) : d.data.first_name;
    })
    .call(wrap, boxSize);

// clicks
svg.selectAll("g.person").on("click", function (d) {
    var person = d.data;
    $('#person-modal .fname').text(person.first_name);
    $('#person-modal .lname').text(person.last_name);
    $('#person-modal .bplace').text(person.birth_place);
    $('#person-modal .bday').text(person.birth_at);
    $('#person-modal .dday').text(person.death_at);
    $('#person-modal .rel').text(person.relation);
    $('#person-modal .fatname').text(person.father_name);
    $('#person-modal .motname').text(person.mother_name);
    $('#person-modal .city').text(person.city);
    $('#person-modal .district').text(person.district);
    $('#person-modal .hometown').text(person.hometown);
    $('#person-modal .mstatus').text(person.marriage_status);
    $('#person-modal .gender').text(person.gender);
    $('#person-modal .status').text(person.status);
    $('#person-modal').modal('show');
});
// mouseover
svg.selectAll("g.person").on("mouseover", function (d) {
    d3.select(this).classed("person-hover", true);
});

// mouse hover
svg.selectAll("g.person").on("mouseout", function (d) {
    d3.select(this).classed("person-hover", false);
});

// Custom path function that creates straight connecting lines.
function elbow(d) {
    return "M" + d.parent.y + "," + d.parent.x +
        "H" + (d.parent.y + (d.y - d.parent.y) / 2) +
        "V" + d.x +
        "H" + d.y;
}

function wrap(text, size) {
    text.each(function () {
        var text = d3.select(this),
            words = text.text().split(/\s+/).reverse(),
            word,
            line = [],
            lineNumber = 0,
            lineHeight = 1.1, // ems
            y = text.attr("y"),
            dy = parseFloat(text.attr("dy")),
            tspan = text.text(null).append("tspan").attr("x", 0).attr("y", y).attr("dy", dy + "em");
        while (word = words.pop()) {
            line.push(word);
            tspan.text(line.join(" "));
            if (tspan.node().getComputedTextLength() > size.width - 20) {
                line.pop();
                tspan.text(line.join(" "));
                line = [word];
                tspan.attr("dy", (dy - 0.6) + "em");
                tspan = text.append("tspan").attr("x", 0).attr("y", y).attr("dy", ++lineNumber * lineHeight + dy + "em").text(word);
            }
        }
    });
}

function zoomFit(paddingPercent) {
    var bounds = svg.node().getBBox();
    var parent = svg.node().parentElement;
    var fullWidth = parent.clientWidth,
        fullHeight = parent.clientHeight;
    var width = bounds.width,
        height = bounds.height;
    var midX = bounds.x + width / 2,
        midY = bounds.y + height / 2;
    if (width == 0 || height == 0) return; // nothing to fit
    var scale = (paddingPercent || 0.75) / Math.max(width / fullWidth, height / fullHeight);
    var translate = [fullWidth / 2 - scale * midX, fullHeight / 2 - scale * midY];
    svg.call(zoom.transform, d3.zoomIdentity.translate(translate[0], translate[1]).scale(scale));
}