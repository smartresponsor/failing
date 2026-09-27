# Shared and framework failure policy

## Principle

Runtime failure evidence is explicit-contract evidence, not a catalogue of every failure that could happen inside Symfony or the host process.

Failing must not automatically attach shared HTTP failures to every operation.

## Security failures

Authentication and authorization failures such as 401 and 403 enter operation inventory only when an owning security/API integration can determine their applicability for a concrete operation.

The integration may implement the same FailureProviderInterface and OperationFailureInventoryProviderInterface contracts used by business consumers.

Failing itself does not inspect firewall configuration, voters, route access-control expressions, or authentication internals to guess operation membership.

## Framework failures

A framework-generated exception or transport failure is not automatically part of the public operation contract.

FailureResolver resolves only explicitly registered exception mappings. Unknown throwables remain unresolved so Symfony keeps its normal exception pipeline.

In particular, Failing does not convert every unknown throwable into an implicit public 500 failure.

A component or host may declare a stable public internal-error failure when that response is intentionally part of its API contract.

## Inventory closure

FailureRegistry answers which public failures are known.

OperationFailureInventory answers which of those known failures belong to a specific operation.

Registration alone never implies operation membership.

Therefore a shared failure may be registered once and attached only to operations for which it is contractually applicable.

## L5 consequence

Exact runtime/OpenAPI status parity must compare declared operation failure evidence, not ambient framework possibilities.

Unexpected infrastructure failures are handled by framework/runtime behavior and may later be covered by an explicitly defined OpenAPI default policy, but they must not silently widen the exact runtime denominator.
