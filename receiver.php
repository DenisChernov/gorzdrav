<?php
require_once "parser.php";
use Gorzdrav\Parser;

$tmp_name = $_FILES["file"]["tmp_name"];
$name = $_FILES["file"]["name"];
move_uploaded_file($tmp_name, "/tmp/$name");

//$filename = "gorzdrav.xlsx";
$filename = "/tmp/$name";

//
$parser = new Parser($filename);
$rows = $parser->getRows();
foreach ($rows as $indexRow => &$row):
    foreach ($row as $indexCell => &$cell):
        if ($indexCell > 1) {
            $cell = "\0".$cell;

//            $parser->replaceTags($cell, ["p", "li"]);
            $parser->makeAbbr($cell, [
                ["search" => "(т\. д\.)", "to" => "т.д."],
                ["search" => "(н М)", "to" => "нМ"],
                ["search" => "(т\. ч\.)", "to" => "т.ч."],
            ]);
            $parser->setSpacesAfterDot($cell, ["т", "д", "\d"], ["н", "мм рт", "ч", "д", "ст", "таб", "пл", "об", "шип", "сусп", "пор", "д\/сусп", "г", "кг", "мл", "л", ".ч", "нМ", ".д"]);
            $parser->uppercaseAfterDot($cell, []);
            $parser->addSpaceBetweenWords($cell, ["нМ"]);
            $parser->addSpaceAfterEndBracket($cell, []);
            $parser->addSpaceBetweenWordAndNumber($cell, []);
            $parser->addSpaceBetweenNumberAndWord($cell, []);
            $parser->addSpaceAfterSimbol($cell, ":", []);
            $parser->addSpaceAfterSimbol($cell, ",", []);
            $parser->removeSemicolonAtSentence($cell, []);
            $parser->removeDoubleSpaces($cell);
            $parser->removeWhiteSpaceAtStart($cell);
            $parser->removeSpaceAtEnd($cell);

        }
    endforeach;
endforeach;

$parser->saveFile($rows);
