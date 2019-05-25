<?php


namespace SzuniSoft\Unas\Internal\Builders;


use Illuminate\Support\Collection;
use SzuniSoft\Unas\Exceptions\TooHighChunkException;
use SzuniSoft\Unas\Internal\ApiSchema;

/**
 * Trait Paginated
 * @package SzuniSoft\Unas\Internal\Builders
 */
trait Paginated
{

    /**
     * @var int
     */
    protected $perRequest = ApiSchema::MAX_PAGING_CHUNK_SIZE;

    /**
     * @param int $perRequest Shown results per request.
     *
     * @return \SzuniSoft\Unas\Internal\Builders\Paginated
     */
    public function paginate(int $perRequest = null)
    {
        $perRequest = $perRequest ?: $this->perRequest;

        if ($perRequest > ApiSchema::MAX_PAGING_CHUNK_SIZE) {
            throw new TooHighChunkException(ApiSchema::MAX_PAGING_CHUNK_SIZE, $perRequest);
        }


        $this->limitNum($perRequest);
        $this->perRequest = $perRequest;

        return $this;
    }

    /**
     * Automatically sends paging requests.
     *
     * @param callable $cb
     * @param null     $until Determine last page. By default it will walk until the last available page.
     */
    public function walkThrough(callable $cb, $until = null)
    {
        $currentPage = 1;

        do {
            /** @var Collection $results */
            $results = $this->page($currentPage);

            if (!$results->isEmpty()) {
                $cb($results);
                $currentPage++;
            }

        } while ($results->isNotEmpty() && ($until === null || $until >= $currentPage));
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return mixed
     */
    public function page(int $page, int $perPage = null)
    {
        if ($perPage) {
            $this->paginate($perPage);
        }
        return $this->limitStart(($page - 1) * $this->perRequest)->retrieve();
    }

    /**
     * @param callable $cb
     * @param int      $perPage
     * @param null     $until
     */
    public function chunk(callable $cb, int $perPage = ApiSchema::MAX_PAGING_CHUNK_SIZE, $until = null)
    {
        $this->paginate($perPage)->walkThrough($cb, $until);
    }

}
