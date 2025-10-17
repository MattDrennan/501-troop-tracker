# Project Roadmap

This document outlines the high-level technical direction for the project over the next few years.

## Year 1-2: Foundational Improvements

### Incrementally Refactor Architecture using ADRs (Architecture Decision Records)

We will adopt an incremental approach (the Strangler Fig pattern) to improving the application's architecture. Instead of a large-scale rewrite, we will refactor the application feature-by-feature. Each refactoring effort will be guided by an Architecture Decision Record (ADR) that documents the target architecture for that specific feature.

**Tasks for `/src`:**

- [ ] **Golden Master Tests:** During conversion generate golden master tests where possible.
- [ ] **Research and Choose ADR Tooling:** Decide on a format and tool for managing ADRs (e.g., `adr-tools`, or simple markdown templates).
- [ ] **Define ADR Process:** Document the process for proposing, reviewing, and accepting new ADRs for the team.
- [ ] **Create ADR for Login Feature:** Write the first ADR to define the target architecture for the login functionality.
- [ ] **Refactor Login Feature:** Implement the changes described in the login ADR, refactoring the existing login pages and code while leaving other features untouched.
- [ ] **Identify and Prioritize Next Feature:** Choose the next feature to be refactored (e.g., user registration, troop reporting).
- [ ] **Repeat Process:** Continue the cycle of creating an ADR and then refactoring the corresponding feature, one by one.
