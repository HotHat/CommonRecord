<?php declare(strict_types=1);


namespace App\Services;


class Money
{
    private $amount = '0.00';
    private $scale = 2;

    public function __construct($amount)
    {
        bcscale(20);
        $this->amount = strval($amount);
    }

    public function setScale($scale) {
        $this->scale = $scale;
        return $this;
    }

    public static function make($amount) {
        return new self($amount);
    }

    public function take() {
        return bcadd($this->amount, '0', $this->scale);
    }

    public function add($amount) {
        $this->amount = bcadd($this->amount, strval($amount));
        return $this;
    }

    public function sub($amount) {
        $this->amount = bcsub($this->amount, strval($amount));
        return $this;
    }

    public function mul($amount) {
        $this->amount = bcmul($this->amount, strval($amount));
        return $this;
    }
    public function div($amount) {
        $this->amount = bcdiv($this->amount, strval($amount));
        return $this;
    }

    public function equal($v) {
        if ($v instanceof Money) {
            return bccomp($this->take(),$v->take(), $this->scale) === 0;
        }

        return bccomp($this->take(), strval($v), $this->scale) === 0;
    }

    public function gt($v) {
        if ($v instanceof Money) {
            return bc($this->take(),$v->take(), $this->scale) === 1;
        }

        return bccomp($this->take(), strval($v), $this->scale) === 1;
    }

    public function lt($v) {
       if ($this->equal($v) || $this->gt($v)) {
           return false;
       }

       return true;
    }


}