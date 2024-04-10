class Result {
    private $status;
    private $result;
    private function __construct($status, $result)
    {
        $this->status = $status;
        $this->result = $result;
    }

    public static function Ok($data) {
        return new Result(true, $data);
    }
    public static function Err($err) {
        return new Result(false, $err);
    }

    public function isOk() : bool {
        return $this->status;
    }

    public function isErr() : bool {
        return !$this->status;
    }

    public function andThen(callable $fn): Result {
        if ($this->status) {
            $this->result = $fn($this->result);
        }
        return $this;
    }

    public function orElse(callable $fn): Result
    {
        if (!$this->status) {
            $this->result = $fn($this->result);
        }
        return $this;
    }

    public function unwrap() {
        return $this->result;
    }
}

// test
$ok = Result::Ok('This is ok result');
$err = Result::Err('This is error Message');

$this->assertTrue($ok->isOk() === true);
$this->assertTrue($err->isOk() === false);
$this->assertTrue($ok->isErr() === false);
$this->assertTrue($err->isErr() === true);

dump($ok->unwrap());
dump($err->unwrap());
