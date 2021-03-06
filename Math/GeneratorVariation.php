<?php

namespace Qwer\LottoDocumentsBundle\Math;

class GeneratorVariation
{

    public function combinationKeys($m, $n)
    {
        //Количество шаров выбраным пользователем
        $countBalls = $m;
        //Количество шаров в комбинации
        $ballInArray = $n;
        //Используется как счетчик для генерации комбинации
        $vector = array();
        //Масссив всех возможных комбинаций
        $outArray = array();

        //??
        $delta = $countBalls - $ballInArray;
        //Счетчик
        $num = 0;
        while ($num < $ballInArray) {
            $vector[$num] = $num;
            $num++;
        }


        //$combination_count = 0;
        //Переменная для выхода из цикла
        $is_nocycle = 0;
        //$num = 0;
        while (($num != $delta) || ($is_nocycle == 0)) {
            $num = 0;
            while ($num < $countBalls) {
                //$combination_count++;
                $i = 0;
                //Массив комбинации
                $combination = array();
                //Цикл для генерации комбинации
                while ($i < $ballInArray) {
                    $num = $vector[$i] + 1;
                    $combination[$i] = $num;
                    $i++;
                }

                $outArray[] = $combination;
                $vector[$ballInArray - 1] = $vector[$ballInArray - 1] + 1;
                $num = $vector[$ballInArray - 1];
            }

            $is_nocycle = 1;
            $i = $ballInArray - 2;
            while ($i >= 0) {
                $num = $vector[$i];
                if ($num < $delta + $i) {
                    $is_nocycle = 0;
                    $vector[$i] = $vector[$i] + 1;
                    $j = $i + 1;
                    while ($j < $ballInArray) {
                        $num = $vector[$j - 1];
                        $vector[$j] = $num + 1;
                        $j++;
                    }
                    break;
                }
                $i--;
            }
            $num = $vector[0];
        }
        return $outArray;
    }

    public function combinations($array, $k)
    {
        $n = count($array);
        $keysArray = $this->combinationKeys($n, $k);

        $combinations = array();
        foreach ($keysArray as $keys) {
            $combination = array();

            foreach ($keys as $key) {
                $combination[] = $array[$key - 1];
            }
            $combinations[] = $combination;
        }

        return $combinations;
    }

    public function allCombinations($balls)
    {
        $results = array(array());
        foreach ($balls as $element) {
            foreach ($results as $combination) {
                if (!in_array(array_merge(array($element), $combination), $results)) {
                    array_push($results, array_merge(array($element), $combination));
                }
            }
        }
        array_shift($results);
        return $results;
    }

    public function combinationsWithoutSingle($balls)
    {
        $preresults = $this->allCombinations($balls);
        $result = array();
        foreach ($preresults as $preresult) {
            if(count($preresult) > 1){
                $result[] = $preresult;
            }
        }
        
        return $result;
    }
}
