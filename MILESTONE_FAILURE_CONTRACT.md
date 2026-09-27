# Failure Contract milestone

## Goal

Create one consumer-agnostic platform mechanism for declaring public failures and producing deterministic runtime failure/status inventory without duplicating Symfony or RFC 9457.

## Phase 1 - foundation

- [x] stable FailureCode value object;
- [x] RFC 9457 FailureType value object;
- [x] immutable FailureDefinitionDTO;
- [x] consumer FailureProviderInterface;
- [x] runtime FailureRegistry;
- [x] declared exception FailureResolver;
- [x] thin Symfony exception subscriber;
- [x] RFC 9457 response renderer;
- [x] operation failure inventory;
- [x] anti-wheel executable guard.

## Phase 2 - consumer calibration

- [ ] migrate one narrow Cruding failure slice;
- [ ] migrate one Billing operation;
- [x] migrate one Paying operation;
- [ ] verify the same Failing contracts need no consumer-specific branch;
- [ ] decide whether an explicit transport-independent failure category has proven value.

## Phase 3 - API contract evidence

- [ ] expose canonical inventory evidence;
- [ ] define exact treatment of shared security failures;
- [ ] define exact treatment of framework-generated failures;
- [ ] define exact-code versus OpenAPI default/range response semantics;
- [ ] materialize L5 runtime/OpenAPI response parity only after the denominator is deterministic.

## Non-goals

- replacing Symfony error handling;
- inventing an alternative Problem Details format;
- centralizing business vocabulary;
- inferring exhaustive failures by scanning controller control flow.
