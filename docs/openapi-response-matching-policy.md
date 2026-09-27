# OpenAPI response matching policy

## Exact declared failures

A runtime failure that is present in deterministic operation evidence has a known concrete HTTP status.

For L5 failure-response parity, that known status requires an exact OpenAPI response entry for the same operation.

Examples:

    runtime 404 -> OpenAPI '404'
    runtime 409 -> OpenAPI '409'
    runtime 422 -> OpenAPI '422'

An OpenAPI range response such as `4XX` or `5XX` does not satisfy an exact known runtime failure.

An OpenAPI `default` response also does not satisfy an exact known runtime failure.

## Range and default responses

Range/default responses may describe ambient, framework, infrastructure, or otherwise non-enumerated behavior.

They are allowed to coexist with exact responses but are excluded from exact declared-failure parity.

They must not widen the runtime denominator and must not hide missing exact documentation.

## OpenAPI-only exact failures

An exact OpenAPI error response that has no matching deterministic runtime failure evidence is contract drift for L5 purposes.

The API owner must either:

- declare the runtime failure and its operation membership; or
- remove/correct the stale exact OpenAPI response.

## Success responses

Failing owns failure contracts only.

This policy does not infer or own successful 2xx/3xx outcome inventories. Full success-status parity is a separate concern and must not be fabricated from Failing data.

The initial L5 rule built on this repository therefore covers declared external API failure statuses, not every possible successful response.
