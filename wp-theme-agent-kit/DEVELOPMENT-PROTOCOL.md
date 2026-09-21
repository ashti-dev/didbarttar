# DEVELOPMENT PROTOCOL

Preferred implementation lifecycle:

1. Baseline
2. RED test/spec proof
3. Implementation
4. GREEN
5. Mutation testing where meaningful
6. Re-proof if tests changed
7. Full regression
8. Check-name diff
9. Diff audit
10. Report
11. Commit/push according to project policy

Rules:
- Never weaken a test to obtain GREEN.
- A surviving meaningful mutant requires investigation.
- A reduction in check count requires investigation.
- Compare check names, not only totals.
- Verify behavioral fixtures come from real project markup where applicable.
- Protected files require explicit approval.
- Unrelated scope must remain untouched.
