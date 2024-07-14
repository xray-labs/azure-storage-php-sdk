<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Queries;

use Closure;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/**
 * @template TManager of Manager
 * @template TReturn of object
 */
class BlobTagQuery
{
    /** @var array<int, array{tag: string, operator: string, value: string}> */
    protected array $wheres = [];

    protected Closure $callback;

    /** @param TManager $manager */
    public function __construct(protected Manager $manager)
    {
        //
    }

    /** @return BlobTagQuery<TManager, TReturn> */
    public function where(string $tag, string $operator, ?string $value = null): self
    {
        if (is_null($value)) {
            $value    = $operator;
            $operator = '=';
        }

        $this->validateOperator($operator);

        $this->wheres[] = ['tag' => $tag, 'operator' => $operator, 'value' => $value];

        return $this;
    }

    /**
     * @param Closure(string $query): TReturn $callback
     * @return BlobTagQuery<TManager, TReturn>
     */
    public function whenBuild(Closure $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /** @return TReturn */
    public function build(): object
    {
        if (!isset($this->callback)) {
            throw RequiredFieldException::missingField('callback');
        }

        usort($this->wheres, fn (array $a, array $b) => $a['value'] <=> $b['value']);

        $queries = [];

        foreach ($this->wheres as $where) {
            $queries[] = "\"{$where['tag']}\"{$where['operator']}'{$where['value']}'";
        }

        $query = urlencode(implode('AND', $queries));

        return ($this->callback)($query);
    }

    protected function validateOperator(string $operator): void
    {
        if (!in_array($operator, ['=', '>', '>=', '<', '<='])) {
            throw new \InvalidArgumentException("Invalid operator: {$operator}");
        }
    }
}
