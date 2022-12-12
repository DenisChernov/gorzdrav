<?php
namespace Gorzdrav;
include "vendor/autoload.php";

use JetBrains\PhpStorm\NoReturn;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;


class Parser {
    private $__filename;
    private SimpleXLSX $xlsx;
    private $__rows;

    public function __construct($filename) {
        $this->setFilename($filename);
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->__filename;
    }

    /**
     * @param mixed $_filename
     */
    public function setFilename($_filename): void {
        $this->__filename = $_filename;
        $this->xlsx = SimpleXLSX::parse($this->__filename);
        $this->__rows = $this->xlsx->rows();
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->__rows;
    }

    public function save($rows, $filename) {
        SimpleXLSXGen::fromArray( $rows )
            ->setDefaultFont( 'Calibri')
            ->setDefaultFontSize(11)
            ->setColWidth(1, 6) // 1 - num column, 35 - size in chars
            ->saveAs($filename);
    }

    /**
     * @return string
     */
    public function toHTML(): string {
        return $this->xlsx->toHTML();
    }

    /**
     * @return SimpleXLSX
     */
    public function getXlsx(): SimpleXLSX
    {
        return $this->xlsx;
    }

    /**
     * Убирает теги
     *
     * @param string $string Входная строка, изменения сохраняются в ней же
     * @param array $tags Массив тегов для замены
     * @return void
     */
    public function replaceTags(string &$string, array $tags): void
    {
        $pattern = "";
        foreach ($tags as $index => $tag):
            $pattern .= "<". $tag. "[^>]*>|<\/$tag>". ($index < count($tags)-1 ? "|" : "");
        endforeach;
        $string = mb_ereg_replace("$pattern", "", $string);
    }

    /**
     * Вставка пробела после точки.
     * Возможны исключения, при которых данное правило не применяется
     *
     * @param string $string Входная строка, изменения сохраняются в ней же
     * @param array $excludesFirstLevel Массив исключений, при которых не нужно вставлять пробел после точки. Для сокращений с точками
     * @param array $excludesSecondLevel Массив исключений, при которых не нужно вставлять пробел после точки
     * @return void
     */
    public function setSpacesAfterDot(string &$string, array $excludesFirstLevel, $excludesSecondLevel): void
    {
        if (count($excludesFirstLevel) || count($excludesSecondLevel)) {
            $pattern = "";
            if (count($excludesFirstLevel)) {
                $pattern .= "(?<!";
                foreach ($excludesFirstLevel as $index => $tag):
                    $pattern .= $tag . ($index < count($excludesFirstLevel) - 1 ? "|" : "");
                endforeach;
                $pattern .= ")";
            }
            if (count($excludesSecondLevel)) {
                $pattern .= "(?<!";
                foreach ($excludesSecondLevel as $index => $tag):
                    $pattern .= $tag . ($index < count($excludesSecondLevel) - 1 ? "|" : "");
                endforeach;
                $pattern .= ")\.(\S)";
            }
        }else {
            $pattern = "\.(\S)";
        }
        $string = mb_ereg_replace($pattern, ". \\1", $string, "m");
    }

    /**
     * Буква в верхнем регистре после точки пробела
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function uppercaseAfterDot(string &$string, array $excludes): void
    {
        $pattern = "/\.\s([а-я])/u";
        $string = preg_replace_callback($pattern, function ($match): string {
            return ". ". mb_strtoupper($match[1]);
        }, mb_substr($string, 0));
    }

    /**
     * Добавление пробела между словами
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function addSpaceBetweenWords(string &$string, array $excludes): void
    {
        $pattern = "";
        if (count($excludes)) {
            $pattern .= "(?!";
            foreach ($excludes as $index => $tag):
                $pattern .= $tag . ($index < count($excludes) - 1 ? "|" : "");
            endforeach;
            $pattern .= ")";
        }
        $pattern .= "([а-я])([А-Я])|([a-z][A-Z])";
        $string = mb_ereg_replace($pattern, "\\1 \\2", $string);
    }

    /**
     * Вставка пробела после закрывающей круглой скобки
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function addSpaceAfterEndBracket(string &$string, array $excludes): void
    {
        $pattern = "\)([а-яА-Я0-9a-zA-Z])";
        $string = mb_ereg_replace($pattern, ") \\1", $string, "mu");
    }

    /**
     * Вставка пробела между Буквой и Цифрой
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function addSpaceBetweenWordAndNumber(string &$string, array $excludes): void
    {
        $pattern = "([а-яА-Яa-zA-Z])(\d{1,})";
        $string = mb_ereg_replace($pattern, "\\1 \\2", $string, 'mu');
    }


    /**
     * Вставка пробела между Цифрой и Буквой
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function addSpaceBetweenNumberAndWord(string &$string, array $excludes): void
    {
        $pattern = "([0-9])([а-яА-Яa-zA-Z])";
        $string = mb_ereg_replace($pattern, "\\1 \\2", $string, "mu");
    }

    /**
     * Вставка запятой после знака
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function addSpaceAfterSimbol(string &$string, $search, array $excludes): void
    {
        $pattern = "$search([а-яА-Яa-zA-Z])";
        $string = mb_ereg_replace($pattern, "$search \\1", $string);
    }

    /**
     * Удаление точки с запятой в начале предложения
     * @param string $string
     * @param array $excludes
     * @return void
     */
    public function removeSemicolonAtSentence(string &$string, array $excludes): void
    {
        $pattern = "^;(.*)";
        $string = mb_ereg_replace($pattern, "\\1", $string, "mu");
    }

    /**
     * Сохранение файла в браузер
     * @param array $rows
     * @return void
     */
    public function saveFile(array $rows): void {
        \Shuchkin\SimpleXLSXGen::fromArray($rows)
            ->setDefaultFont( 'Calibri')
            ->setDefaultFontSize(11)
            ->setColWidth(1, 6) // 1 - num column, 35 - size in chars
            ->downloadAs('file.xlsx');
        exit();
    }

    /**
     * Удаление двойных пробелов
     * @param string $string
     * @return void
     */
    public function removeDoubleSpaces(string &$string): void
    {
        $pattern = "(.)\s{2,}(.)";
        $string = mb_ereg_replace($pattern, "\\1 \\2", $string, "mu");
    }

    /**
     * Удаление пробела в начале строки
     * @param string $string
     * @return void
     */
    public function removeWhiteSpaceAtStart(string &$string): void
    {
        $pattern = "^(\s+| )(.*)";
        $string = mb_ereg_replace($pattern, "\\2", $string, "mu");
        if (str_starts_with($string, " ")) {
            $string = substr($string, 1);
        }
    }

    /**
     * Удаление пробела в конце предложения
     * @param string $string
     * @return void
     */
    public function removeSpaceAtEnd(string &$string): void
    {
        $pattern = "(.*)(\.\s)$";
        $string = mb_ereg_replace($pattern, "\\1.", $string, "mu");
    }

    /**
     * Формирование сокращений
     *
     * @param string $string
     * @param array $abbr
     * @return void
     */
    public function makeAbbr(string &$string, array $abbr): void
    {
        foreach ($abbr as $var):
            $string = mb_ereg_replace($var["search"], $var["to"], $string, "mu");
        endforeach;
    }

    /**
     * Грязная замена того, что нельзя заменить регуляркой
     * @param $string
     * @param array $fromStrings
     * @param array $toStrings
     * @return void
     */
    public function dirtyHackReplace(string &$string, array $fromStrings, array $toStrings)
    {
        for ($index = 0; $index < count($fromStrings); $index++):
            $string = str_replace($fromStrings[$index], $toStrings[$index], $string);
        endfor;

    }
}

