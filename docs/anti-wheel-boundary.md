# Anti-wheel boundary

## Principle

Failing standardizes contracts; it does not reimplement framework or protocol machinery.

## Owned here

- immutable failure declaration shape;
- a minimal safe machine-token envelope for consumer-owned failure codes;
- provider aggregation and duplicate detection;
- declared exception-to-failure lookup;
- operation-to-failure membership;
- deterministic runtime failure/status inventory;
- thin integration with Symfony exception events;
- RFC 9457 projection of an already-resolved failure.

## Explicitly not owned here

- an alternative Symfony exception dispatcher;
- a replacement HttpException hierarchy;
- a private catalogue of HTTP status constants;
- replacement Response, HttpKernel, routing, or Security machinery;
- a custom error JSON protocol instead of RFC 9457;
- consumer-specific failure catalogues;
- static control-flow inference that guesses possible failures from arbitrary PHP code.

Integration with Symfony and RFC 9457 is allowed. Reimplementation is not.

The executable guard under tools/guard rejects known framework-replacement declarations and business/consumer vocabulary leaking into src/.
