# CONTRIBUTING.md

## Welcome, Trooper!

Thank you for your interest in contributing to the **501st Troop Tracker**! Your operational support is highly valued. Adherence to these guidelines ensures a swift and efficient contribution process.

## Code of Conduct

All contributors are expected to uphold the 501st Legion's standards of professionalism and respect. Please review and adhere to our [Code of Conduct](CODE_OF_CONDUCT.md) (***Note: You may need to create this file if it doesn't exist***).

---

## Contribution Methods

### 1. Issue Reporting (Bugs & Enhancements)

Before submitting code, you must first create a corresponding Issue:

* **Bugs:** Open an issue labeled **`bug`**. Include steps to reproduce, screenshots, and relevant environment details.
* **Enhancements/Features:** Open an issue labeled **`enhancement`** or **`feature`**. Explain the feature's operational benefit and discuss major changes first.
* **Refactor:** Open an issue labeled **`refactor`**. Explain the change in core functionality.


### 2. Submitting Code Changes (Pull Requests)

For all code submissions, follow this structured process precisely:

1.  **Fork and Clone:** Fork the repository and clone your copy locally.

2.  **Create a Branch:** The branch name **MUST** use the commit type followed by the issue number.
    * **Format:** `<type>-<issue-number>`
    * **Accepted Types (matching commit types):** `fix`, `feat`, `docs`, `refactor`, `chore`, etc.
    * **Example:** If you are fixing bug `#42`, your branch should be named **`fix-42`**.

3.  **Make Changes:** Write your code, adhering to existing styling and conventions.

4.  **Test:** Validate your changes thoroughly in your local development environment.

5.  **Commit Your Changes:** **All commit messages MUST follow the Angular Commit Convention and include the issue number directly in the subject line for traceability.**

    #### 📝 Commit Message Format (Simplified)
    Your commit message must be formatted to include the type, scope, subject, and the issue number for direct correlation.

    ```
    <type>(<scope>): <subject> (#<issue-number>)

    [optional body explaining WHY the change was made]
    ```

    | Component | Description | Example |
    | :--- | :--- | :--- |
    | **type** (Required) | The **nature** of the change. | `feat`, `fix`, `docs` |
    | **scope** (Optional) | The **area** of the codebase affected. | `ui`, `api`, `database` |
    | **subject** (Required) | A concise description, **including the issue number as a suffix.** | `correct event date display on mobile (#1234)` |

    **Example Commit:**
    ```
    fix(ui): correct troop event date display for mobile users (#1234)

    The previous logic failed to properly localize the event date when viewed
    on mobile devices due to an inconsistent datetime format in the payload.
    This change ensures UTC is consistently used for parsing.
    ```
    *(Note: Using `(#<issue-number>)` ensures the commit links to the issue without prematurely closing it, leaving that action for the PR.)*

6.  **Create a Pull Request (PR):**
    * Open a Pull Request from your branch (e.g., `fix-1234`) to the **`main`** branch.
    * **Reference the Issue:** Ensure the PR description explicitly states **`Closes #1234`** (or `Fixes #1234`) to automatically close the issue upon merge.

---

## Code Review & Merge

The project maintainer or designated reviewers will assess all Pull Requests. You may be required to make revisions; this is standard procedure to ensure stability. Your patience and cooperation ensure the tracker remains a high-quality resource.

Thank you for your service and adherence to protocol!