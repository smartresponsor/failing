# Consumer calibration

## Paying — implemented pilot

Current real surface:

- operation: GET /api/payments/{id}
- existing runtime failure: payment-not-found
- existing HTTP status: 404
- existing duplication: controller, service, and local error-response factory

Calibration implementation:

- consumer-owned code: payment-not-found
- consumer-owned type: urn:paying:problem:payment-not-found
- shared DTO: FailureDefinitionDTO
- shared runtime index: FailureRegistry
- shared operation evidence: OperationFailureInventory
- derived evidence: GET /api/payments/{id} + payment.payment_not_found + 404

Result: no Paying-specific branch was added to Failing.

The existing Paying response payload is intentionally unchanged in this pilot. Rendering migration is a separate compatibility step.

## Cruding — implemented exception-driven pilot

Observed current implementation:

- CrudApiExceptionSubscriber owns Symfony exception interception;
- CrudApiProblemResponseFactory owns local Problem Details rendering;
- the stable not-found contract already exposes code `crud_not_found`, type `urn:cruding:problem:crud_not_found`, and HTTP 404.

Calibration implementation:

- consumer-owned code and type are preserved without renaming;
- CrudFailureProvider maps Symfony NotFoundHttpException to FailureDefinitionDTO;
- shared FailureRegistry and FailureResolver resolve the declaration without Cruding-specific branches in Failing;
- a regression test verifies that the declaration matches the existing CrudApiProblemResponseFactory output exactly.

The current CrudApiExceptionSubscriber and CrudApiProblemResponseFactory remain behaviorally unchanged. Grammar-backed operation membership remains deferred because generic Failing must not expand Cruding route grammar itself.

## Billing — read-only calibration candidate

Observed current implementation:

- BillingInvoiceEndpoint chooses 400, 403, 404, 422, and 429 directly;
- BillingErrorPayloadFactory owns concrete business error profiles;
- OpenAPI responses repeat the same HTTP-status knowledge.

Expected migration boundary:

- Billing keeps concrete failure codes, titles, retryability/business metadata, and operation membership;
- Failing supplies the common declaration and inventory mechanism;
- later L5 compares derived runtime status inventory with canonical OpenAPI responses.

No mutation was performed because the Billing worktree contains substantial parallel active changes.

## Calibration finding

The first real consumer does not require a custom FailureCategory abstraction.

A direct standard HTTP status in FailureDefinitionDTO is sufficient for deterministic runtime inventory. A transport-independent semantic category remains deferred until multiple consumers demonstrate a concrete need.

The second calibration candidate exposed a vocabulary-boundary correction: Failing must not require dotted failure codes. Existing consumers already use stable hyphenated and underscored public codes. FailureCode therefore validates only a safe lower-case machine-token envelope while preserving the consumer-owned spelling.
