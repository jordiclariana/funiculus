<?php


namespace Sergigp\Funiculus {
    function op($op, $arg = null)
    {
        $fns = [
            'inc'       => function($x) { return ++$x; },
            'dec'       => function($x) { return --$x; },
            'square'    => function($x) { return $x * $x; },
            'pow'       => function($x, $y) { return pow($x, $y); },
            '+'         => function($x, $y) { return $x + $y; },
            '-'         => function($x, $y) { return $x - $y; },
            '*'         => function($x, $y) { return $x * $y; },
            '/'         => function($x, $y) { return $x / $y; },
            'pos'       => function($x) { return $x > 0; },
            'neg'       => function($x) { return $x < 0; },
            'odd'       => function($x) { return $x % 2 !== 0; },
            'even'      => function($x) { return $x % 2 === 0; },
        ];

        if (!array_key_exists($op, $fns)) {
            throw new \InvalidArgumentException(sprintf('Unknown operator: %s', $op));
        }

        $fn = $fns[$op];

        if (is_null($arg)) {
            return $fn;
        }

        return function ($a) use ($fn, $arg) {
            return $fn ($a, $arg);
        };
    }

    function first($sq)
    {
        return is_array($sq) ? current($sq) : $sq->current();
    }

    function rest($sq)
    {
        if (!is_array($sq)) {
            $sq = iterator_to_array($sq);
        }

        return array_slice($sq, 1);
    }

    function cons($e, array $sq)
    {
        // @TODO support for iterators and generators?
        array_unshift($sq, $e);
        return $sq;
    }

    function is_empty($sq)
    {
        return get_count($sq) === 0;
    }

    function reduce(callable $fn, $sq)
    {
        $sq = is_array($sq) ? $sq : iterator_to_array($sq);

        $r = first($sq);

        for ($i = 0; $i < (count($sq) - 1); $i++) {
            $r = $fn($r, $sq[$i + 1]);
        }

        return $r;
    }

    function get_count ($sq)
    {
        return is_array($sq) ? count($sq) : iterator_count($sq);
    }

    function map(callable $fn, $sq)
    {
        foreach ($sq as $e) {
            yield $fn($e);
        }
    }

    function filter(callable $fn, $sq)
    {
        foreach ($sq as $e) {
            if ($fn($e)) yield $e;
        }
    }

    function every(callable $fn, $sq)
    {
        foreach ($sq as $e) {
            if (!$fn($e)) {
                return false;
            }
        }

        return true;
    }

    function some(callable $fn, $sq)
    {
        foreach ($sq as $e) {
            if ($fn($e)) {
                return true;
            }
        }

        return false;
    }

    function take($n, $sq)
    {
        $i = 1;

        foreach ($sq as $e) {
            if ($i > $n) {
                break;
            }
            $i++;
            yield $e;
        }
    }

    function repeat($e)
    {
        while (true) {
            if (is_callable($e)) {
                yield $e();
            } else {
                yield $e;
            }
        }
    }

    function progression(callable $fn)
    {
        $i = -1;
        while (true) {
            $i++;
            yield $fn($i);
        }
    }

    function take_while(callable $fn, $sq)
    {
        foreach ($sq as $e) {
            if ($fn($e)) {
                yield $e;
            } else {
                return;
            }
        }
    }

    function drop_while(callable $fn, $sq)
    {
        foreach ($sq as $e) {
            if (!$fn($e)) {
                yield $e;
            }
        }
    }
}
