<?php
include "vendor/autoload.php";

use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

require_once "parser.php";
use Gorzdrav\Parser;

$filename = "gorzdrav.xlsx";


$parser = new Parser($filename);
$rows = $parser->getRows();
$row = $rows[2];
$rowNew = $rows[2];
//print_r($row);
foreach ($row as $indexCell => $cell):
    if ($indexCell > 1) {
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

        $rowNew[$indexCell] = $cell;
    }
endforeach;
foreach ($rowNew as $indexCell => $cell):
    if ($cell !== "") {
        echo "was: \n";
        print_r($row[$indexCell]);
        echo "\nnew: \n";
        print_r($cell);
        echo "\n\n";
    }
endforeach;
//print_r($rowNew);
