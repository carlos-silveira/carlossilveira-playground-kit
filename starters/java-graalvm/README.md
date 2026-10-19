# Java 21 GraalVM Native Binary Starter (`java-graalvm`) — `carlossilveira.dev/lab/__LAB_SLUG__/`

Java 21 starter compiled ahead-of-time via GraalVM `native-image --static --libc=musl` in GitHub Actions to run with zero JVM warm-up overhead on Spaceship shared hosting (`server58.shared.spaceship.host`), or routed via CNAME to an external container.

## Local Execution

```bash
java Main.java
```
