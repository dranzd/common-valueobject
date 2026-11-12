# Search

Namespace `Dranzd\Common\Domain\ValueObject\Search`. Building blocks for describing a search request
(`Query`: a `Page` plus filters and sorters) and its outcome (`Result`: items plus a total). The library
does not execute searches; it only carries the request and the response.

## Criteria, Filter, Sorter

```php
interface Criteria
{
    public function sameValueAs(Criteria $criteria): bool;
    public function __toString(): string;
    public static function fromCodeAndValueOrNull(string $code, mixed $value): ?Criteria;
    public function isForFiltering(): bool;
    public function isForSorting(): bool;
}

interface Filter extends Criteria {}

interface Sorter extends Criteria
{
    public static function inAscendingOrder(): Sorter;
    public static function inDescendingOrder(): Sorter;
    public function getOrder(): int; // SORT_ASC or SORT_DESC
}
```

Concrete criteria are your own classes. `fromCodeAndValueOrNull` turns a (code, value) pair, for example
from a query string, into a criterion, or returns null when the pair is not understood.

## FilterTrait

Provides `isForFiltering()` (true), `isForSorting()` (false) and `sameClassAs(object): bool` (exact class
comparison). The using class must itself implement `sameValueAs()`, `__toString()` and the static
`fromCodeAndValueOrNull()`.

## SorterTrait

Provides `inAscendingOrder()`, `inDescendingOrder()`, `fromCodeAndValueOrNull()`, `sameValueAs()`,
`isForFiltering()` (false), `isForSorting()` (true), `sameClassAs()` and `getOrder()`.

Contract for the using class:

- a property named `$order` holding `SORT_ASC` (4) or `SORT_DESC` (3);
- a constructor whose first argument is that order (the factories call `new static(SORT_ASC)`);
- `__toString()`;
- a `FILTER_CODE` constant (the same constant name is used for sorters and filters) to be buildable by code.

`fromCodeAndValueOrNull($code, $value)` returns a sorter only when the class defines `FILTER_CODE`, `$code`
equals it, and `$value` is exactly the integer `SORT_ASC` or `SORT_DESC`. A string such as `'4'` from a
query string does not match: cast it with `(int)` first. A class without `FILTER_CODE` never matches.

`sameValueAs()` is true for the same exact class with the same order (compared with `===` on the stored value).

The trait file does not declare `strict_types`, so the calls it makes keep PHP's scalar coercion: a using class whose
constructor or `$order` is typed `string` still receives `SORT_ASC` from the factories, and `getOrder()` returns an
untyped numeric-string order such as `'4'` as the integer 4.

## CriteriaProviderTrait

Builds criteria from (code, value) pairs. The using class assigns the private properties `$filters` and
`$sorters` (usually in its constructor) with class names of `Filter` / `Sorter` implementations.

```php
final public function filterFromCodeAndValueOrNull(string $code, mixed $value): ?Filter;
final public function sorterFromCodeAndValueOrNull(string $code, mixed $value): ?Sorter;
```

Each registered class is asked in order and the first non-null `Filter` / `Sorter` wins. Lists that were
never assigned count as empty. A registered class that answers with something that is not a `Filter` / `Sorter`
is skipped and the next one is asked. Assign the lists in the constructor and do not redeclare the properties: the
trait declares them without a native type or default, and a redeclaration with any type, default or visibility
other than an identical untyped `private $filters;` is a fatal error.

## Page

```php
public function __construct(int $number, int $limit);
public static function startDefault(): Page;      // page 1, limit 0
public function startFrom(int $number): Page;
public function limitTo(int $limit): Page;
public function getStartingNumber(): int;
public function getLimit(): int;
```

Immutable: `startFrom` and `limitTo` return new instances. A page number of 0 throws
`InvalidArgumentException`. Negative page numbers and limits are accepted and not validated (kept for
compatibility). What a limit of 0 means is left to the consumer; `Page` does not interpret it.

## Query

```php
public function __construct(?Page $page = null, array $filters = [], array $sorters = []);
public static function with(array $filters, array $sorters, int $pageNumber, int $itemsPerPage): Query;
public function forPageNumber(int $pageNumber): Query;
public function limitToItemsPerPage(int $itemsPerPage): Query;
public function filterBy(array $filters): Query;
public function sortBy(array $sorters): Query;
public function getPage(): Page;
public function getFilters(): array;
public function getSorters(): array;
```

Without a page the query is page 1 with 10 items per page. All "modifier" methods return a clone and leave the
original (including its `Page`) untouched. `filterBy` and `sortBy` replace the whole list. Elements are
intended to be `Filter` / `Sorter` instances but are not validated at runtime.

## Result

```php
public function __construct(array $items, int $total);
public function getItems(): array;
public function getTotal(): int;
public function toArray(): array; // ['items' => [...], 'total' => int]
```

`toArray()` converts items one level deep: an object with `toArray()` (preferred) or `toNative()` is replaced
by that method's result; scalars, arrays (including objects inside them) and other objects are returned as is.
Keys are preserved.

## Example

```php
final class StatusFilter implements Filter
{
    use FilterTrait;

    public const FILTER_CODE = 'status';

    public function __construct(public readonly string $status) {}

    public static function fromCodeAndValueOrNull(string $code, mixed $value): ?Criteria
    {
        return $code === self::FILTER_CODE && is_string($value) ? new self($value) : null;
    }

    public function sameValueAs(Criteria $criteria): bool
    {
        return $criteria instanceof self && $criteria->status === $this->status;
    }

    public function __toString(): string { return 'status=' . $this->status; }
}

final class CreatedSorter implements Sorter
{
    use SorterTrait;

    public const FILTER_CODE = 'created';

    private int $order;

    public function __construct(int $order) { $this->order = $order; }

    public function __toString(): string { return 'created:' . ($this->order === SORT_ASC ? 'asc' : 'desc'); }
}

final class OrderCriteria
{
    use CriteriaProviderTrait;

    public function __construct()
    {
        $this->filters = [StatusFilter::class];
        $this->sorters = [CreatedSorter::class];
    }
}

$provider = new OrderCriteria();
$filters = $sorters = [];
foreach (['status' => 'paid', 'created' => '3', 'unknown' => 'x'] as $code => $value) {
    if ($f = $provider->filterFromCodeAndValueOrNull($code, $value)) { $filters[] = $f; }
    if ($s = $provider->sorterFromCodeAndValueOrNull($code, (int) $value)) { $sorters[] = $s; }
}

echo implode(',', array_map('strval', $filters)), "\n";   // status=paid
echo implode(',', array_map('strval', $sorters)), "\n";   // created:desc

$query = Query::with($filters, $sorters, 2, 25);
echo $query->getPage()->getStartingNumber(), ' ', $query->getPage()->getLimit(), "\n"; // 2 25

$next = $query->forPageNumber(3);
echo $query->getPage()->getStartingNumber(), ' ', $next->getPage()->getStartingNumber(), "\n"; // 2 3

echo json_encode((new Result([['id' => 1], 'x'], 40))->toArray()), "\n"; // {"items":[{"id":1},"x"],"total":40}

var_dump(CreatedSorter::fromCodeAndValueOrNull('created', '4')); // NULL (string, not int)
```
