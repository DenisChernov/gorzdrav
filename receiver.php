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
//            $parser->replaceTags($cell, ["p", "li"]);
            $parser->makeAbbr($cell, [
                ["search" => "(т\. д\.)", "to" => "т.д."],
                ["search" => "(н М)", "to" => "нМ"],
                ["search" => "(т\. ч\.)", "to" => "т.ч."],
            ]);
            $parser->setSpacesAfterDot($cell, ["т", "д", "\d"],
                ["н", "мм рт", "ч", "д", "ст", "таб", "пл", "об", "шип", "сусп", "пор", "д\/сусп", "г", "кг", "мл", "л", ".ч", "нМ", ".д"]
            );
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
            $parser->dirtyHackReplace(
                $cell,
                ["ч. В упаковке", "д. В упаковке", "ст. В упаковке", "таб. В упаковке", "пл. В упаковке", "об. В упаковке", "шип. В упаковке", "сусп. В упаковке", "пор. В упаковке", "д/сусп. В упаковке", "г. В упаковке", "кг. В упаковке", "мл. В упаковке", "л. В упаковке", "т.ч. НМ"],
                ["ч. в упаковке", "д. в упаковке", "ст. в упаковке", "таб. в упаковке", "пл. в упаковке", "об. в упаковке", "шип. в упаковке", "сусп. в упаковке", "пор. в упаковке", "д/сусп. в упаковке", "г. в упаковке", "кг. в упаковке", "мл. в упаковке", "л. в упаковке", "т.ч.\nнМ"]
            );
            $cell = "\0".$cell;
        }
    endforeach;
endforeach;

$parser->saveFile($rows);
