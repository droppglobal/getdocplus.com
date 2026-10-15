<?php



$operators = array('not', '!', 'or', '||', '&&', 'and', '|', '^', '&', '==', '===', '!=', '!==', '<', '>', '>=', '<=', 'not in', 'in', '..', '+', '-', '~', '*', '/', '%', 'matches', '**');
$operators = array_combine($operators, array_map('strlen', $operators));
arsort($operators);

$regex = array();
foreach ($operators as $operator => $length) {
            $regex[] = preg_quote($operator, '/').(ctype_alpha($operator[$length - 1]) ? '(?=[\s(])' : '');
}

echo '/'.implode('|', $regex).'/A';
