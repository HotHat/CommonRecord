<?php declare(strict_types=1);

use Tests\TestCase;

class RmbTest extends TestCase
{
    public function test_rmb()
    {
        $this->assertEquals($this->toRmb('9000100010000000001.01'), '玖佰兆零壹佰万亿零壹佰亿零壹元零壹分');
        $this->assertEquals($this->toRmb('9001000010000000001.01'), '玖佰兆零壹仟万亿零壹佰亿零壹元零壹分');
        $this->assertEquals($this->toRmb('9001000010001001001.01'), '玖佰兆零壹仟万亿零壹佰亿零壹佰万零壹仟零壹元零壹分');
        $this->assertEquals($this->toRmb('9000000000000000001.01'), '玖佰兆零壹元零壹分');
        $this->assertEquals($this->toRmb('1000000.01'), '壹佰万元零壹分');
        $this->assertEquals($this->toRmb('0.53'), '零元伍角叁分');
        $this->assertEquals($this->toRmb('0.00'), '零元整');
        $this->assertEquals($this->toRmb('1409.50'), '壹仟肆佰零玖元伍角');
        $this->assertEquals($this->toRmb('6007.14'), '陆仟零柒元壹角肆分');
        $this->assertEquals($this->toRmb('1680.32'), '壹仟陆佰捌拾元叁角贰分');
        $this->assertEquals($this->toRmb('107000.53'), '壹拾万零柒仟元伍角叁分');
        $this->assertEquals($this->toRmb('16409.02'), '壹万陆仟肆佰零玖元零贰分');
        $this->assertEquals($this->toRmb('325.04'), '叁佰贰拾伍元零肆分');
        $this->assertEquals($this->toRmb('1680.32'), '壹仟陆佰捌拾元叁角贰分');
        $this->assertEquals($this->toRmb('6007.14'), '陆仟零柒元壹角肆分');
        $this->assertEquals($this->toRmb('150107001.53'), '壹亿伍仟零壹拾万零柒仟零壹元伍角叁分');
    }

    private function toRmb(string $num)
    {
        $numCN = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        $g4CN = ['', '拾', '佰', '仟'];
        $w4CN = ['元', '万', '亿', '万亿', '兆'];

        //精确到分后面就不要了，所以只留两个小数位
        $num = bcadd($num, '0', 2);

        //将数字转化为整数
        $num = bcmul($num, '100', 0);

        $numLen = strlen($num);
        // 最大到百兆
        if ($numLen > 21) {
            throw new \Exception("最大到百兆");
        }

        $fen = intval($num[$numLen - 1]);
        $jiao = intval($num[$numLen - 2]);

        // 补全4位
        $pad = intval(ceil(($numLen - 2) / 4)) * 4;

        $numPad = [];


        $idx = 0;
        for ($i = $numLen - 3; $i >= 0; $i--) {
            $numPad[$idx] = $num[$i];
            $idx++;
        }

        for (; $idx < $pad; $idx++) {
            $numPad[] = '0';
        }

        // 4个一组
        $g4 = [];
        $gi = [];
        for ($i = 1; $i <= $pad; $i++) {
            $gi[] = $numPad[$i-1];
            if ($i % 4 === 0) {
                $g4[] = $gi;
                $gi = [];
            }
        }

        $level = intval($pad / 4);
        $st = [];
        $g4Len = count($g4);
        for ($gIdx = $g4Len-1; $gIdx >= 0; $gIdx--) {
            $v = $g4[$gIdx];

            $isZero = $v[3] == '0' && $v[2] == '0' && $v[1] == '0' && $v[0] == '0';
            $isFistLevel = $level == ($gIdx + 1);
            $isPrefixZero = $v[3] == '0';

            if ($isZero) {
                if ($isFistLevel) {
                    $cnStr = $numCN[0] . $w4CN[$gIdx];
                } elseif ($gIdx == 0) { // 0元但万以上有值
                    $cnStr = '元';
                } else {
                    $cnStr = '';
                }
            } else {
                $cnStr = '';
                // 开头的0不要
                $lastIsZero = $isPrefixZero;
                $prefixHasNum = false;
                // 千位到个位
                for ($i = 3; $i >= 0; $i--) {
                    $vi = intval($v[$i]);
                    if ($vi != 0) {
                        if ($lastIsZero && $prefixHasNum) {
                            $cnStr .=  $numCN[0];
                        }
                        $cnStr .=  $numCN[$vi] . $g4CN[$i];
                    }
                    if ($vi == 0) {
                        $lastIsZero = true;
                    } else {
                        $lastIsZero = false;
                        $prefixHasNum = true;
                    }
                }

                // 开头补0
                if (!$isFistLevel) {
                    $prefixLevel = $st[$gIdx+1];
                    if ($prefixLevel['suffix_zero']) {
                        $cnStr = $numCN[0] . $cnStr;
                    }
                }

                $cnStr .= $w4CN[$gIdx];
            }

            $st[$gIdx] = [
                // 个位0
                'suffix_zero' => $v[0] == '0',
                'is_zero' => $isZero,
                'is_first' => $isZero,
                'cn' => $cnStr
            ];
        }

        $cnStr = implode('', array_column($st, 'cn'));
        $isZero = empty($st);
        // 0元但有角或分
        if ($isZero && ($fen != 0 || $jiao != 0)) {
            $cnStr .= $numCN[0] . $w4CN[0];

        }

        if (!$isZero && $fen == 0 && $jiao == 0) {
            $cnStr .= '整';
        }

        // 0元且没有有角和分
        if ($isZero && ($fen == 0 || $jiao == 0)) {
            $cnStr .= $numCN[0] . $w4CN[0] . '整';
        }


        if ($jiao != 0) {
            $cnStr .= $numCN[$jiao] . '角';
        }

        if ($fen != 0) {
            $cnStr .= ($jiao == 0 ? $numCN[0] : '') . $numCN[$fen] . '分';
        }

        return $cnStr;
    }
}
