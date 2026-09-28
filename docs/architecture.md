# Failing architecture

## Boundary

Failing owns the generic failure contract and runtime inventory mechanism.
Consumer repositories own their business failure vocabulary and operation membership.

Dependency direction:

    consumer -> failing/failure
    failing/failure -X-> consumer

Failing must not enumerate Billing, Shipping, Paying, Ordering, Cruding, or any other consumer vocabulary.

## Core objects

- FailureCode validates only a safe lower-case machine-token envelope; the consumer owns whether its stable vocabulary uses dots, underscores, or hyphens.
- FailureType carries the RFC 9457 problem type URI-reference.
- FailureDefinitionDTO is immutable declaration data: code, type, standard HTTP status, title, optional exception mapping.
- FailureProviderInterface is implemented by consumers to supply declarations.
- FailureRegistry aggregates providers at runtime; it is not a source-code catalogue.
- FailureResolver resolves only declared exception mappings.
- FailureProblemResponseRenderer renders an already-resolved declaration as RFC 9457 application/problem+json.
- FailureExceptionSubscriber is a thin Symfony kernel.exception adapter and does not replace Symfony error handling.
- OperationFailureInventoryDTO declares which failure codes belong to one METHOD + path operation and whether that membership is complete; completeness defaults to false.
- OperationFailureInventory derives deterministic METHOD + path + failure code + HTTP status evidence for later OpenAPI parity.
- OperationFailureInventoryExporter publishes schema v2 with both failure rows and independent operation coverage, so a complete operation with zero failures is still machine-visible.

## Deliberate omission: FailureCategory

The first contract does not introduce a custom FailureCategory -> HTTP status taxonomy.
Such an enum would duplicate standard HTTP semantics unless a transport-independent use is demonstrated.
This is a milestone decision, not an accidental omission.
