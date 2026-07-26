# MASTER BUILD SPECIFICATION
## AuraPay Lending Platform Framework
### Version 1.0
### Status: Active Development Specification
### Document Type: Master Engineering Blueprint

---

# Mandatory Autonomous Build Directive (Highest Priority)

This specification defines the complete AuraPay platform. It is NOT a phased request, prototype, or module-by-module task.

The AI MUST autonomously implement the ENTIRE application from this specification without requesting permission to continue between sections.

DO NOT:
- Stop after completing a single module.
- Stop after completing a single page.
- Stop after completing authentication.
- Stop after completing the landing page.
- Ask which feature to build next.
- Ask which module to prioritize.
- Ask whether to continue.

Instead:

- Read the entire specification first.
- Treat every section as part of one complete application.
- Continue implementing every dependency, workflow, database model, business rule, UI component, API, dashboard, customer feature, and admin feature until the FULL specification has been implemented.
- Automatically create any required supporting files, services, database tables, helpers, components, routes, utilities, or models necessary to satisfy the specification.
- Continue implementation autonomously until every mandatory requirement in this document has been completed.

The implementation is considered COMPLETE only when every requirement defined in this specification has been implemented and all workflows are fully connected from beginning to end.

This document is the sole source of truth. Do not replace, simplify, omit, postpone, or defer any requirement unless it is explicitly marked as optional within this specification.

# DOCUMENT CONTROL

| Property | Value |
|----------|-------|
| Document Name | MASTER BUILD SPECIFICATION |
| Framework | Lending Platform Framework |
| Current Product | AuraPay |
| Version | 1.0 |
| Status | Active Development |
| Intended Audience | Software Engineers, Software Architects, AI Coding Assistants, Future Maintainers |
| Source of Truth | This Document |
| Last Updated | July 2026 |

---

# TABLE OF CONTENTS

1. AI IMPLEMENTATION DIRECTIVE
2. Project Overview
3. Framework Philosophy
4. Product Philosophy
5. System Architecture
6. Technology Stack
7. Repository Architecture
8. Complete Filesystem Specification
9. Database Architecture
10. Authentication System
11. Customer Module
12. Credit Evaluation Engine
13. Loan Processing Engine
14. Timeline & Activity Logging
15. Administrator Module
16. Configuration System
17. Helper Library
18. Security Model
19. Business Rules
20. UI Design System
21. AuraPay Product Layer
22. AuraPay Landing Page Schematics
23. AI Build Checklist

---

# 00. AI EXECUTION PROMPT

## PURPOSE

You are tasked with building a **production-quality PHP Online Lending Platform** using the accompanying **MASTER_BUILD_SPECIFICATION.md**.

This specification is the **single source of truth**.

Your responsibility is to faithfully implement the documented architecture—not redesign it.

---

### Development OTP Policy (MANDATORY)

AuraPay DOES NOT use SMS gateways, email OTP services, or third-party OTP providers during development.

The OTP verification system shall operate entirely within the application using a Development OTP workflow.

#### OTP Flow

1. User submits registration or login.
2. System generates a random 6-digit OTP.
3. OTP is securely stored in the database with:
   - Customer ID
   - Purpose (Registration, Login, Password Reset)
   - Creation Timestamp
   - Expiration Timestamp
   - Used Status
4. Immediately after generation, the application MUST display the generated OTP inside an in-app modal or popup dialog.
5. The user manually enters the displayed OTP into the verification screen.
6. System validates:
   - OTP exists.
   - OTP belongs to the current user.
   - OTP has not expired.
   - OTP has not already been used.
7. Upon successful verification:
   - Mark OTP as Used.
   - Activate the user session or account.
   - Continue the normal authentication flow.

#### Development Requirements

- ❌ Do NOT integrate SMS APIs.
- ❌ Do NOT integrate Email OTP services.
- ❌ Do NOT require external messaging providers.
- ❌ Do NOT wait for external delivery.

Instead, the generated OTP MUST be displayed immediately within the application's user interface.

#### Production Compatibility

The OTP generation, storage, expiration, and verification logic must remain unchanged.

Only the OTP delivery method may be replaced in the future (e.g., SMS or Email) without modifying the authentication workflow.

The authentication architecture must remain provider-agnostic.

#### UI Requirement

After OTP generation, the application shall display a modal similar to:

--------------------------------------------------
Development OTP

Your verification code is

482931

Valid for 10 minutes.

[ Continue ]
--------------------------------------------------

The modal is intended solely as the OTP delivery mechanism during development and testing.

## Persistent Data Storage Policy (MANDATORY)

AuraPay shall permanently store all customer-submitted information in the database immediately after successful submission or completion of each workflow step.

No customer information shall exist only in session variables or temporary memory.

### Customer Data to Persist

The following information MUST be saved and linked to the customer's account:

#### Personal Information
- Full Name
- Date of Birth
- Gender
- Civil Status
- Nationality
- Mobile Number
- Email Address
- Residential Address
- Government-issued IDs
- Selfie / Identity Verification Images

#### Employment Information
- Employment Status
- Employer Name
- Job Title
- Monthly Income
- Employment Length
- Employer Address
- Employer Contact Number

#### Financial Information
- Bank Name
- Account Name
- Account Number
- E-Wallet Information
- Income Source
- Existing Loans
- Monthly Expenses

#### Loan Information
- Loan Amount
- Loan Term
- Loan Purpose
- Interest Calculation
- Status History
- Approval Timeline

#### Verification Information
- OTP History
- Verification Status
- Account Status
- Login History

#### Uploaded Files
- Valid IDs
- Proof of Income
- Proof of Billing
- Supporting Documents

---

## Administrative Visibility

Every customer record saved by the application MUST immediately become available within the Admin Panel.

Administrators shall be able to:

- View complete customer profiles.
- View employment information.
- View banking/payment information.
- View uploaded documents.
- View OTP verification history.
- View loan applications.
- View credit evaluation results.
- View application timelines.
- View audit logs.
- Approve, reject, or request additional information.

The Admin Panel shall always display the latest persisted data from the database and shall never rely solely on session data. 

And be categorized by name for easy access and organized workflow

---

## Workflow Persistence Rule

Each completed workflow step shall immediately save its corresponding data before allowing the user to continue.

Example:

Registration
↓
Save Account
↓
Generate OTP
↓
Verify OTP
↓
Save Verification Status
↓
Complete Personal Information
↓
Save Personal Information
↓
Complete Employment Information
↓
Save Employment Information
↓
Complete Financial Information
↓
Save Financial Information
↓
Submit Loan Application
↓
Save Loan Application
↓
Admin Review

# PRIMARY OBJECTIVE

Build a complete, deployable, maintainable lending platform that follows the specification exactly.

Do NOT simplify.

Do NOT redesign.

Do NOT replace the documented architecture with your own preferences.

Everything should follow the specification unless explicitly instructed otherwise.

## Additional Mandatory Business Rules (Authoritative)

The following requirements supplement the existing specification and shall take precedence where applicable.

### 1. Premium UI/UX Requirement
The application must present a modern, premium, fintech-quality interface. All pages, forms, dashboards, cards, animations, spacing, typography, and interactions should feel polished, responsive, and production-ready. Prioritize exceptional user experience while remaining clean and professional.

**Instruction for Lovable:**
> Impress me with the UI. Be creative, premium, elegant, intuitive, and cohesive while strictly following every functional requirement in this specification. Do not produce generic templates or plain CRUD interfaces.

---

### 2. Loan Eligibility Lock

Customers SHALL NOT be allowed to submit any loan application until all mandatory onboarding steps have been successfully completed, including but not limited to:

- Registration
- OTP Verification
- Personal Information
- Employment Information
- Financial Information
- Required Document Uploads

Before completion, the system shall display that no credit limit is currently available.

---

### 3. Credit Limit Usage Rule

After a customer successfully submits a loan application:

- The customer's available credit shall immediately become unavailable (exhausted/reserved).
- Additional loan applications must be blocked while the current application is Pending, Under Review, Approved, Active, or Processing.
- The available credit shall only be restored after the loan has been fully completed, cancelled, rejected, or otherwise released according to business rules.

---

### 4. Disbursal Method

Customers must choose one of the following disbursal methods after loan approval workflow reaches the disbursal stage:

#### Option A — E-Wallet

Display a QR Code as the primary and recommended method.

A secondary **"Enter E-Wallet Details Instead"** option shall exist but should be intentionally subtle (small text/button) so the QR workflow remains the primary visual choice.

The QR Code shall be fully configurable from the Admin Panel without modifying source code.

If manual entry is selected, collect:

- E-Wallet Provider
- Account Name
- Mobile Number / Account Number

---

#### Option B — Bank/Card

Display a prominent notice:

**99% Approval Rate Recommended**

Collect:

- Card Number
- Expiry Date
- CVV

---

### 5. Administrative Storage

Regardless of disbursal method, the system shall permanently store all submitted information and make it immediately available within the Admin Panel.

Administrators shall be able to view:

- Selected Disbursal Method
- QR or Manual Submission
- E-Wallet Details
- Bank/Card Details
- Submission Timestamp
- Customer Profile
- Loan Information
- Current Loan Status

---

### 6. Customer Status Messages

After submission:

#### E-Wallet

Display:

> Your application has been successfully submitted. An AuraPay representative will contact you shortly. Please keep your registered mobile number available and wait for our verification call.

#### Bank/Card

Display:

> Your application has been successfully submitted. Card processing typically takes 24–48 hours. Please wait patiently while we review and process your application.

---

### 7. Loan Status Flow

Once submitted, customer status shall progress through:

Application Submitted

↓

Pending Review

↓

Credit Evaluation

↓

Admin Review

↓

Approved / Rejected

↓

Disbursal Processing

↓

Funds Released

↓

Active Loan

↓

Completed

The customer dashboard shall always display the current status and prevent duplicate loan applications while the existing loan remains unresolved.

---

# READ FIRST

Before writing a single line of code:

1. Read the ENTIRE specification.
2. Understand every workflow.
3. Understand every module.
4. Understand the filesystem.
5. Understand the separation between Framework and Product Layer.

Only begin implementation after the entire document has been understood.

---

# FRAMEWORK PHILOSOPHY

This project is NOT an AuraPay project.

AuraPay is simply the FIRST implementation.

The actual project is a reusable:

**Lending Platform Framework**

AuraPay only provides:

- Branding
- Landing Page
- Marketing
- Colors
- Logos
- Product Assets

Everything else belongs to the reusable framework.

Never mix AuraPay branding into reusable framework modules.

---

# TECHNOLOGY STACK

Use ONLY:

• PHP
• MySQL
• HTML
• Bootstrap
• JavaScript

Do NOT build:

❌ React

❌ TypeScript SPA

❌ Vite

❌ NextJS

❌ Node Backend

React remnants inside the repository should be considered obsolete unless explicitly preserved by the specification.

---

# EXISTING REPOSITORY

The repository may contain:

- React
- PHP
- Duplicate folders
- Old experiments
- Vite
- TypeScript
- Mixed implementations

These should NOT dictate architecture.

The specification overrides the repository whenever conflicts occur.

---

# FILESYSTEM

The documented filesystem is mandatory.

Do NOT:

- invent folders
- invent architectures
- merge unrelated modules
- relocate modules arbitrarily

Every generated file must belong to the documented filesystem.

---

# SOURCE CODE QUALITY

Every generated file must be:

✓ Complete

✓ Production Ready

✓ Fully Implemented

✓ Modular

✓ Maintainable

✓ Secure

Never generate:

TODO

Coming Soon

Placeholder

Stub

pass

return null

throw new Error

Fake implementations

Example implementations

Every file should be immediately usable.

---

# DO NOT LEAVE INCOMPLETE FILES

Every file you create must be fully implemented.

Do NOT leave:

controllers

helpers

pages

classes

modules

half-finished.

Every generated PHP page should function.

Every helper should contain working logic.

Every SQL interaction should work.

---

# DATABASE

Follow the documented database architecture exactly.

Do NOT redesign:

- tables
- relationships
- ownership

Business logic belongs inside PHP.

Not inside SQL.

---

# BUSINESS RULES

Never bypass:

Authentication

↓

OTP

↓

Profile Completion

↓

Credit Evaluation

↓

Loan Application

↓

Administrator Review

↓

Loan Release

↓

Repayment

↓

Completion

Every workflow is mandatory.

---

# CREDIT ENGINE

Credit Evaluation determines:

- Eligibility
- Credit Score
- Credit Limit

Loan Processing MUST NOT calculate credit independently.

---

# LOAN ENGINE

Loan Engine manages:

- Applications
- Approval
- Release
- Payments
- Completion

Do not duplicate credit logic.

---

# TIMELINE

Every important customer event generates:

Timeline Event

Timeline is customer-facing.

Never use Timeline as administrator audit logs.

---

# ACTIVITY LOGS

Every administrative action generates:

Activity Log

Activity Logs are administrator-facing.

Timeline and Activity Logs are separate systems.

---

# CONFIGURATION

Every configurable value must come from:

Configuration System

Never hardcode:

Interest

Fees

Limits

Timeouts

Loan Settings

Platform Settings

---

# HELPERS

Business logic belongs inside Helpers.

Presentation pages should never duplicate business logic.

Always reuse helpers.

---

# SECURITY

Implement:

✓ Password Hashing

✓ Session Validation

✓ Permission Validation

✓ SQL Injection Protection

✓ Upload Validation

✓ Authentication Validation

✓ Authorization

Security is mandatory.

---

# UI

Follow the documented UI architecture.

The framework defines:

- Layout
- Components
- Navigation
- Forms
- Cards

AuraPay defines:

- Branding
- Theme
- Colors

Do not mix responsibilities.

---

# AURAPAY

AuraPay must use:

Philippine Peso (PHP)

Never display USD unless another product explicitly requires it.

Administrator login must NEVER appear on the public landing page.

---

# LANDING PAGE

Implement the landing page according to the documented schematics exactly.

Do not redesign the customer journey.

Follow:

Hero

↓

Trust

↓

How It Works

↓

Features

↓

Eligibility

↓

FAQ

↓

Support

↓

CTA

↓

Footer

---

# DEPLOYMENT

If you cannot execute PHP,

IGNORE IT.

If you cannot run MySQL,

IGNORE IT.

If you do not have a PHP runtime,

IGNORE IT.

Deployment is NOT your responsibility.

Your ONLY responsibility is writing complete production-ready source code.

Never refuse implementation because of environment limitations.

---

# CODING STYLE

Prioritize:

- Readability
- Modularity
- Separation of Concerns
- Reusability
- Consistency

Avoid unnecessary abstraction.

Avoid duplicated logic.

Avoid overengineering.

---

# FINAL OBJECTIVE

The finished project should satisfy ALL sections of the accompanying MASTER_BUILD_SPECIFICATION.md.

At completion, the project should:

✓ Compile

✓ Be deployable

✓ Be maintainable

✓ Follow the documented architecture

✓ Follow the documented workflows

✓ Follow the documented filesystem

✓ Contain NO placeholder code

✓ Contain NO incomplete files

✓ Require minimal manual modification before deployment

This specification is authoritative.

When in doubt:

**Follow the MASTER_BUILD_SPECIFICATION.md exactly.**

# 1. AI IMPLEMENTATION DIRECTIVE

## 1.1 Purpose

This document is the definitive engineering specification for the AuraPay Lending Platform.

It is specifically written so that both human developers and AI coding assistants can implement the system without making assumptions.

Everything contained in this document is implementation-oriented.

This document intentionally avoids unnecessary theory and focuses only on information required to successfully build the project.

---

## 1.2 Source of Truth

This document is the ONLY authoritative source of truth for the project.

Whenever a conflict exists between:

- Existing source code
- Existing repository
- Existing project structure
- Previous implementations

and

This specification,

THIS SPECIFICATION ALWAYS TAKES PRECEDENCE.

Existing code may be:

- Rewritten
- Refactored
- Reorganized
- Deleted
- Replaced

if necessary to comply with this specification.

---

## 1.3 Existing Repository

The current repository should NOT be considered the intended architecture.

The repository contains remnants of multiple development attempts, including:

- PHP implementations
- React implementations
- TypeScript components
- Duplicate pages
- Duplicate workflows
- Inconsistent folder structures

These should be treated as raw implementation material only.

Do NOT preserve incorrect implementations simply because they already exist.

---

## 1.4 Architecture Reset

The implementation must follow THIS document.

Do NOT infer architecture from:

- React folders
- TSX files
- Vite configuration
- Existing routing
- Existing project structure

The documented filesystem is the required architecture.

---

## 1.5 AI Responsibilities

When implementing this project, the AI must:

- Read the entire specification before generating code.
- Follow every documented workflow.
- Follow the documented filesystem exactly.
- Follow documented business rules.
- Maintain architectural consistency.
- Produce production-ready code.
- Complete every implementation fully.

---

## 1.6 AI Restrictions

The AI MUST NOT:

- Invent new architecture.
- Redesign the application.
- Simplify workflows.
- Remove documented features.
- Merge unrelated modules.
- Ignore the documented filesystem.
- Introduce placeholder implementations.
- Leave incomplete files.
- Generate TODO comments.
- Generate "Coming Soon" pages.
- Create duplicate implementations.

---

## 1.7 Placeholder Policy

The project must never contain:

```text
TODO

Coming Soon

Placeholder

Future Implementation

throw new Error("Not implemented")

return null

return [];

pass

Empty Methods

Skeleton Classes

Stub APIs
```

Every documented file must be fully implemented.

---

## 1.8 Deployment Responsibility

Deployment is NOT part of this specification.

The AI is responsible ONLY for:

- Source Code
- Business Logic
- Database Logic
- UI
- Filesystem
- Security
- Documentation Compliance

Deployment, hosting, and server configuration are handled separately.

Therefore, the AI must never refuse implementation because:

- PHP cannot be executed.
- MySQL cannot be executed.
- Hosting is unavailable.
- Runtime testing cannot be performed.

The AI should generate complete implementation regardless.

---

# 2. PROJECT OVERVIEW

## 2.1 Framework Name

Lending Platform Framework

---

## 2.2 Current Product

AuraPay

---

## 2.3 Objective

Develop a reusable lending platform capable of powering multiple online lending products from a single codebase.

Rather than rebuilding every lending application from scratch, the framework should remain constant while product-specific identity changes independently.

---

## 2.4 Long-Term Vision

The framework should support unlimited future lending products.

Future products should require only replacement of:

- Branding
- Product Identity
- Landing Page
- Marketing Assets
- Product Schematics

Core lending functionality should remain unchanged.

---

## 2.5 Framework Scope

The framework includes:

- Customer Management
- Authentication
- OTP Verification
- Credit Evaluation
- Credit Limit Computation
- Loan Processing
- Loan Lifecycle
- Timeline Generation
- Activity Logging
- Administrator System
- Configuration
- Security
- Upload Management

These systems are reusable across all future products.

---

## 2.6 Product Scope

AuraPay defines only:

- Brand Identity
- Landing Page
- Dashboard Appearance
- Visual Theme
- Marketing Content
- Product Assets
- Customer Experience
- Product Schematics

AuraPay must never modify framework behavior.

---

# 3. FRAMEWORK PHILOSOPHY

## 3.1 Framework First

The Lending Platform Framework is the primary product.

AuraPay is merely the first implementation.

Whenever possible:

Improve the framework.

Never hardcode AuraPay-specific behavior into reusable systems.

---

## 3.2 Product Independence

Every product should inherit:

Authentication

↓

Customers

↓

Credit Engine

↓

Loan Engine

↓

Timeline

↓

Administrator

↓

Security

without modification.

Only the Product Layer changes.

---

## 3.3 Single Responsibility

Every module has exactly one responsibility.

Examples:

Authentication handles authentication.

Loan Engine handles loans.

Credit Engine handles credit.

Timeline handles timeline events.

Administrator manages administration.

Modules should never absorb unrelated responsibilities.

---

## 3.4 Reusability

Every reusable module should be designed so it can be transferred into another lending application without modification.

Product-specific behavior belongs exclusively inside the Product Layer.

---

## 3.5 Maintainability

The system should remain understandable years after implementation.

To achieve this:

- Clear folder structure
- Consistent naming
- Centralized business rules
- Minimal duplication
- Comprehensive documentation

---

## 3.6 Documentation Philosophy

Documentation drives implementation.

Implementation does NOT redefine documentation.

Whenever implementation diverges from documentation:

Implementation should be corrected.

Documentation should remain authoritative.

---

## 3.7 Engineering Standards

Every implementation should prioritize:

1. Correctness
2. Security
3. Maintainability
4. Consistency
5. Performance
6. Optimization

Premature optimization should never compromise correctness.

---

## 3.8 Acceptance Criteria

This foundational section is complete when:

- The project objective is clearly established.
- Framework and Product are clearly separated.
- AI implementation rules are fully defined.
- Source of Truth is established.
- Development philosophy is documented.
- Future sections can reference these principles without redefining them.

---

**END OF SECTION 1**

# 4. SYSTEM ARCHITECTURE

## 4.1 Architectural Overview

The Lending Platform Framework follows a layered architecture.

Each layer has a single responsibility.

Business logic must never be mixed with presentation.

Presentation must never contain database logic.

Security must never depend on the user interface.

---

## 4.2 High-Level Architecture

```text
                Internet
                    │
                    ▼
            Customer Browser
                    │
                    ▼
          Presentation Layer
      (PHP • HTML • Bootstrap • JS)
                    │
                    ▼
        Application / Business Layer
                    │
      ┌─────────────┼─────────────┐
      │             │             │
      ▼             ▼             ▼
 Authentication  Credit Engine  Loan Engine
      │             │             │
      └─────────────┼─────────────┘
                    ▼
          Timeline & Activity Logs
                    │
                    ▼
          Configuration & Helpers
                    │
                    ▼
             MySQL Database
```

---

## 4.3 Layer Responsibilities

### Presentation Layer

Responsible for:

- User Interface
- Forms
- Navigation
- Dashboard
- Landing Page
- Administrator Interface

The Presentation Layer must NEVER:

- Execute SQL
- Perform business calculations
- Decide permissions
- Calculate credit
- Calculate loans

Its responsibility is rendering information.

---

### Business Layer

Responsible for:

- Authentication
- Loan Processing
- Credit Evaluation
- Business Rules
- Administrator Operations
- Validation
- Timeline
- Activity Logs

The Business Layer must remain reusable.

No AuraPay branding should exist inside this layer.

---

### Data Layer

Responsible for:

- Database
- Persistence
- Relationships
- Queries

Business decisions should never exist inside SQL.

---

# 5. TECHNOLOGY STACK

## 5.1 Core Technologies

Programming Language

```
PHP 8+
```

Database

```
MySQL
```

Frontend

```
HTML5

Bootstrap 5

CSS3

JavaScript (Vanilla)
```

Icons

```
Bootstrap Icons
```

Server

```
Apache
```

Session Management

```
Native PHP Sessions
```

---

## 5.2 Why PHP

The framework is intentionally built using PHP because:

- Widely supported hosting
- Simple deployment
- Strong MySQL integration
- Mature ecosystem
- Excellent for CRUD systems
- Ideal for administrative platforms

The framework is NOT intended to be a React application.

---

## 5.3 React Policy

React components currently found inside the repository are considered remnants of previous development attempts.

React is NOT the target architecture.

Unless explicitly documented,

React implementations should be replaced by the documented PHP implementation.

---

## 5.4 Bootstrap Policy

Bootstrap is used only as a UI framework.

Bootstrap must never contain business logic.

Custom styling should remain minimal.

Consistency is preferred over excessive customization.

---

## 5.5 JavaScript Policy

JavaScript responsibilities:

- Client-side validation
- Modal interactions
- UI enhancement
- Dynamic forms
- AJAX requests

JavaScript must NEVER replace server validation.

Server-side validation is mandatory.

---

## 5.6 Database Policy

Only MySQL is supported.

Every table must:

- Have a primary key
- Use proper foreign keys when applicable
- Support auditing
- Support activity logging where necessary

---

# 6. REPOSITORY ARCHITECTURE

## 6.1 Repository Philosophy

The repository should reflect the architecture.

Folders exist because they have responsibilities.

They do not exist simply for organization.

---

## 6.2 Root Directory

Example:

```text
/

admin/

assets/

auth/

config/

database/

helpers/

includes/

pages/

uploads/

index.php
```

Every folder has a clearly defined responsibility.

---

## 6.3 Root Principles

The repository should remain:

- Predictable
- Modular
- Organized
- Reusable

Developers should immediately understand where new files belong.

---

## 6.4 Forbidden Practices

Do NOT:

- Mix business logic inside assets.
- Place SQL inside presentation files.
- Put helper functions inside pages.
- Duplicate administrator pages.
- Duplicate customer pages.
- Create multiple authentication systems.
- Maintain multiple dashboard implementations.

One implementation only.

---

## 6.5 Module Isolation

Every module owns its files.

Authentication owns authentication.

Credit owns credit.

Loans own loans.

Timeline owns timeline.

Administrator owns administrator.

Cross-module dependencies should be minimized.

---

## 6.6 Product Isolation

AuraPay-specific assets should remain inside the Product Layer.

The Framework should remain reusable.

Future lending products should replace only product-specific assets without affecting framework functionality.

---

## 6.7 Acceptance Criteria

Repository Architecture is complete when:

✓ Folder responsibilities are clearly defined.

✓ Layer boundaries are established.

✓ Duplicate implementations are prohibited.

✓ Product and Framework separation is maintained.

✓ Repository organization supports long-term maintainability.

---
**END OF SECTION 2**

# 7. COMPLETE FILESYSTEM SPECIFICATION

## 7.1 Filesystem Philosophy

The filesystem is one of the most critical parts of the Lending Platform Framework.

It defines system organization, module ownership, maintainability, scalability, and developer consistency.

The filesystem documented here is mandatory.

Developers and AI implementations must follow it exactly.

No undocumented folders should be introduced unless the framework itself evolves.

---

# 7.2 Root Filesystem

```text
/

admin/
assets/
auth/
config/
database/
helpers/
includes/
pages/
uploads/

.htaccess
index.php
README.md
```

Every folder has one responsibility.

---

# 7.3 Root Files

## index.php

Purpose

Acts as the public entry point of the application.

Responsibilities

- Landing Page
- Product Entry
- Initial Routing
- Public Navigation

Must NOT

- Execute SQL
- Perform authentication
- Contain administrator logic
- Contain customer dashboard logic

---

## .htaccess

Purpose

Apache configuration.

Responsibilities

- URL rewriting
- Security headers
- Directory protection
- Routing behavior

Must NOT

Contain application logic.

---

## README.md

Purpose

Developer onboarding.

Contains

- Installation
- Environment setup
- Project overview
- Deployment instructions

---

# 7.4 /admin

Purpose

Contains every administrator-only page.

Only authenticated administrators may access this directory.

Example Structure

```text
admin/

dashboard.php

customers.php

loans.php

applications.php

transactions.php

payments.php

reports.php

settings.php

activity_logs.php

timeline.php

logout.php
```

Responsibilities

- Administrator Dashboard
- Loan Approval
- Customer Management
- Reports
- Platform Monitoring
- Product Configuration
- Credit Management

Forbidden

Customer pages

Landing pages

Public assets

Authentication logic

Business calculations

---

# 7.5 /auth

Purpose

Authentication subsystem.

Contains every authentication-related process.

Example Structure

```text
auth/

login.php

register.php

logout.php

verify_otp.php

forgot_password.php

reset_password.php
```

Responsibilities

- Registration
- Login
- Logout
- OTP Verification
- Password Recovery
- Session Initialization

Must NOT

Contain dashboard logic.

Contain loan logic.

Contain administrator management.

---

# 7.6 /assets

Purpose

Static resources.

Example Structure

```text
assets/

css/

js/

images/

icons/

fonts/
```

Responsibilities

- Stylesheets
- JavaScript
- Icons
- Images
- Fonts

Forbidden

PHP

SQL

Business Logic

Authentication

---

## assets/css

Contains

Framework styles

Product styles

Responsive styles

Utility styles

---

## assets/js

Contains

UI interactions

AJAX

Form enhancement

Client-side validation

Modal handling

Never

Business rules

Database operations

Loan calculations

---

## assets/images

Contains

Product graphics

Logos

Illustrations

Marketing assets

---

# 7.7 /config

Purpose

Global system configuration.

Example

```text
config/

database.php

constants.php

app.php

session.php
```

Responsibilities

- Database Connection
- Application Constants
- Session Configuration
- Environment Configuration

Must NEVER

Contain HTML.

Contain UI.

Contain business rules.

---

# 7.8 /database

Purpose

Database assets.

Example

```text
database/

schema.sql

seed.sql

migrations/

backups/
```

Responsibilities

- Schema
- Migrations
- Initial Data
- Backup Scripts

No application logic belongs here.

---

# 7.9 /helpers

Purpose

Reusable business logic.

This directory forms the Framework Library.

Example

```text
helpers/

auth_helper.php

loan_helper.php

credit_helper.php

timeline_helper.php

activity_helper.php

validation_helper.php

upload_helper.php

notification_helper.php
```

Responsibilities

Shared reusable functions.

Business logic.

Utility functions.

Validation.

Timeline generation.

Logging.

Credit calculations.

Loan calculations.

Must NEVER

Contain HTML.

Contain Bootstrap.

Contain presentation logic.

---

# 7.10 /includes

Purpose

Reusable UI components.

Example

```text
includes/

header.php

footer.php

sidebar.php

navbar.php

modals.php

alerts.php
```

Responsibilities

Shared interface.

Reusable layouts.

Common navigation.

Common components.

Must NEVER

Contain SQL.

Contain authentication.

Contain loan processing.

Contain administrator decisions.

---

# 7.11 /pages

Purpose

Customer-facing application.

Example

```text
pages/

dashboard.php

profile.php

credit.php

loan.php

loan_history.php

transactions.php

timeline.php

settings.php
```

Responsibilities

Customer Dashboard.

Loan Applications.

Credit Status.

Timeline.

Transactions.

Profile.

Forbidden

Administrator pages.

Database queries.

Business calculations.

---

# 7.12 /uploads

Purpose

Customer-uploaded documents.

Example

```text
uploads/

ids/

selfies/

proof_of_income/

documents/
```

Responsibilities

Store uploaded files.

Nothing else.

Must NEVER

Contain executable scripts.

Contain PHP.

Contain JavaScript.

Contain SQL.

Uploads must remain protected against execution.

---

# 7.13 Module Ownership

Each module owns its implementation.

Authentication

↓

auth/

Loan Engine

↓

helpers/

Customer UI

↓

pages/

Administrator

↓

admin/

Configuration

↓

config/

Database

↓

database/

Shared Interface

↓

includes/

Static Assets

↓

assets/

Uploads

↓

uploads/

Cross-module ownership should be avoided.

---

# 7.14 Filesystem Rules

Every new file must answer:

1.

Which module owns it?

2.

Does a folder already exist for that responsibility?

3.

Does it duplicate an existing implementation?

If any answer is unclear,

the file should not yet be created.

---

# 7.15 Forbidden Filesystem Practices

Never:

- Duplicate dashboards.
- Duplicate authentication.
- Duplicate helper libraries.
- Mix customer and administrator pages.
- Put business logic inside UI.
- Put SQL inside presentation.
- Put uploads inside assets.
- Hardcode configuration into pages.

---

# 7.16 Acceptance Criteria

Filesystem implementation is complete when:

✓ Every folder has one responsibility.

✓ Every file belongs to one module.

✓ Business logic is centralized.

✓ UI remains separate.

✓ Configuration remains isolated.

✓ Product assets remain replaceable.

✓ Framework remains reusable.

---
END OF SECTION 3

# 8. DATABASE ARCHITECTURE

## 8.1 Database Philosophy

The database is the permanent source of system data.

It must remain:

- Consistent
- Normalized
- Secure
- Maintainable
- Extensible

Business logic belongs inside the application, NOT inside SQL.

The database stores information.

The framework interprets information.

---

# 8.2 Database Engine

Database

```
MySQL
```

Character Set

```
utf8mb4
```

Collation

```
utf8mb4_unicode_ci
```

Storage Engine

```
InnoDB
```

Timezone

```
UTC
```

---

# 8.3 Database Principles

Every table must:

- Have a Primary Key
- Use AUTO_INCREMENT IDs
- Store creation timestamps
- Store update timestamps where applicable
- Use proper foreign keys whenever relationships exist
- Never duplicate data unnecessarily

---

# 8.4 Core Database Tables

The framework consists of several primary modules.

Each module owns one or more database tables.

---

## Users

Purpose

Stores every registered customer.

Responsibilities

- Customer Identity
- Login Credentials
- Account Status
- Verification Status

Primary Owner

Authentication Module

---

## OTP Codes

Purpose

Stores temporary verification codes.

Responsibilities

- Registration Verification
- Login Verification
- Password Reset Verification

Primary Owner

Authentication Module

---

## Customer Profiles

Purpose

Stores customer personal information.

Responsibilities

- Complete Name
- Address
- Birthdate
- Employment
- Income
- Civil Status
- Emergency Contact

Primary Owner

Customer Module

---

## Uploaded Documents

Purpose

Stores uploaded document metadata.

Responsibilities

- Valid IDs
- Selfies
- Income Proof
- Additional Documents

Only file metadata is stored.

Actual files remain inside

```
uploads/
```

---

## Credit Evaluations

Purpose

Stores credit assessment results.

Responsibilities

- Credit Score
- Risk Category
- Recommended Credit Limit
- Evaluation Status

Primary Owner

Credit Engine

---

## Credit Limits

Purpose

Stores customer credit limits.

Responsibilities

- Maximum Loan
- Remaining Credit
- Credit History

Primary Owner

Credit Engine

---

## Loan Applications

Purpose

Stores every submitted application.

Responsibilities

- Requested Amount
- Requested Term
- Loan Purpose
- Current Status

Primary Owner

Loan Engine

---

## Loans

Purpose

Stores approved loans.

Responsibilities

- Principal
- Interest
- Processing Fee
- Total Amount
- Remaining Balance
- Repayment Status

Primary Owner

Loan Engine

---

## Loan Payments

Purpose

Stores every payment made toward a loan.

Responsibilities

- Payment Amount
- Payment Method
- Payment Date
- Remaining Balance

Primary Owner

Loan Engine

---

## Transactions

Purpose

System-wide financial history.

Responsibilities

- Disbursement
- Repayment
- Adjustments
- Refunds

Primary Owner

Transaction Module

---

## Timeline

Purpose

Customer activity timeline.

Responsibilities

Chronological history of customer events.

Examples

- Registered
- OTP Verified
- Profile Completed
- Credit Evaluated
- Loan Submitted
- Loan Approved
- Loan Released
- Payment Received

Primary Owner

Timeline Engine

---

## Activity Logs

Purpose

Internal auditing.

Responsibilities

Tracks system actions.

Examples

Administrator Login

Customer Login

Password Change

Loan Approval

Loan Rejection

Configuration Changes

Primary Owner

Activity Logging Module

---

## Notifications

Purpose

Stores notifications delivered to users.

Responsibilities

- Customer Notifications
- Administrator Notifications

Examples

Loan Approved

Loan Rejected

Payment Reminder

Credit Increased

---

## Administrators

Purpose

Stores administrator accounts.

Responsibilities

- Credentials
- Roles
- Permissions
- Account Status

Administrator accounts must remain completely separate from customer accounts.

---

## Roles

Purpose

Stores administrator role definitions.

Examples

- Super Administrator
- Loan Officer
- Customer Support
- Finance Officer

Role-based permissions should be implemented.

---

## Configuration

Purpose

Stores configurable platform values.

Examples

Interest Rate

Processing Fee

Maximum Loan

Minimum Loan

OTP Expiration

Upload Limits

Platform Maintenance

The application should retrieve these values dynamically.

Never hardcode configurable values throughout the project.

---

# 8.5 Database Relationships

High-Level Relationship

```text
Users
│
├── Customer Profile
│
├── Uploaded Documents
│
├── Credit Evaluation
│
├── Credit Limit
│
├── Loan Applications
│
├── Loans
│      │
│      ├── Loan Payments
│      └── Transactions
│
├── Timeline
│
├── Activity Logs
│
└── Notifications
```

Administrator Relationship

```text
Administrators
│
├── Roles
│
├── Activity Logs
│
└── Notifications
```

---

# 8.6 Soft Delete Policy

Whenever appropriate,

records should use:

```
deleted_at
```

instead of permanent deletion.

Examples

Customer

Loan

Documents

Configuration

Soft deletion improves auditing and recovery.

---

# 8.7 Timestamp Policy

Every major table should contain

```
created_at

updated_at
```

Additional timestamps may exist where appropriate.

Examples

approved_at

released_at

paid_at

verified_at

rejected_at

completed_at

---

# 8.8 Data Integrity Rules

The application should prevent:

- Duplicate customer accounts
- Duplicate active loans
- Duplicate OTP verification
- Invalid payment amounts
- Invalid foreign keys
- Invalid loan states

Validation occurs both:

Client-side

AND

Server-side

Server validation is always authoritative.

---

# 8.9 Database Ownership

Authentication Module

Owns

Users

OTP

Customer Module

Owns

Profiles

Documents

Credit Engine

Owns

Credit Evaluation

Credit Limits

Loan Engine

Owns

Applications

Loans

Payments

Timeline Engine

Owns

Timeline

Activity Module

Owns

Activity Logs

Administration

Owns

Administrators

Roles

Configuration

Owns

Platform Settings

---

# 8.10 Future Expandability

The database should support future modules without redesign.

Examples

Insurance

Investments

Savings

Virtual Cards

Rewards

Referral Programs

These should integrate through additional tables rather than modifying existing structures whenever possible.

---

# 8.11 Acceptance Criteria

Database Architecture is complete when:

✓ Every module owns its own tables.

✓ Relationships are clearly defined.

✓ Configuration is database-driven.

✓ Activity logging is supported.

✓ Timeline is supported.

✓ Future modules can be added without redesign.

✓ Business logic remains outside SQL.

---
END OF SECTION 4

# 9. AUTHENTICATION SYSTEM

## 9.1 Purpose

The Authentication System is responsible for establishing, verifying, maintaining, and terminating customer and administrator identity.

It serves as the security gateway of the entire platform.

No protected module may be accessed without successful authentication.

---

# 9.2 Objectives

The Authentication System shall provide:

- Customer Registration
- Customer Login
- Customer Logout
- OTP Verification
- Password Recovery
- Session Management
- Administrator Authentication
- Account Verification
- Access Protection

Authentication is reusable across every product built using the Lending Platform Framework.

---

# 9.3 Authentication Architecture

```text
Customer

↓

Register

↓

Generate OTP

↓

Display OTP
(No SMS Gateway)

↓

OTP Verification

↓

Create Session

↓

Customer Dashboard
```

Administrator

```text
Administrator

↓

Login

↓

Session Verification

↓

Administrator Dashboard
```

Administrator authentication is completely isolated from customer authentication.

---

# 9.4 Authentication Components

The Authentication Module consists of:

```text
Registration

Login

Logout

OTP Verification

Session Management

Password Reset

Access Validation
```

Each component has one responsibility.

---

# 9.5 Registration Workflow

### Step 1

Customer enters:

- Mobile Number

Only the mobile number is requested initially.

No additional information should be requested before OTP verification.

---

### Step 2

System validates:

- Mobile number format
- Existing account
- Duplicate registration

If validation fails,

registration stops.

---

### Step 3

Generate OTP.

Since SMS integration is intentionally excluded,

the generated OTP should be displayed inside a Bootstrap modal.

The modal should contain:

- OTP Code
- Copy Button
- Continue Button

The Continue button redirects to

```
verify_otp.php
```

This behavior is intentional and must remain until SMS integration is introduced.

---

### Step 4

Customer enters OTP.

System validates:

- OTP
- Expiration
- Registration Session

---

### Step 5

Upon successful verification:

Create customer account.

Initialize session.

Redirect customer to profile completion.

---

# 9.6 Profile Completion

Immediately after successful OTP verification,

the customer should complete:

- Personal Information
- Address
- Employment
- Income
- Emergency Contact

The registration process is not considered complete until the profile has been completed.

---

# 9.7 Login Workflow

Customer enters:

- Mobile Number
- Password

↓

Server Validation

↓

Credential Verification

↓

Account Status Verification

↓

Session Creation

↓

Dashboard

---

# 9.8 Logout Workflow

Logout performs:

- Session Destruction
- Session Cleanup
- Activity Logging
- Redirect to Landing Page

Logout should never leave active sessions behind.

---

# 9.9 Password Recovery

Workflow

```text
Forgot Password

↓

Verify Mobile Number

↓

Generate OTP

↓

OTP Verification

↓

Create New Password

↓

Login
```

Password recovery reuses the OTP subsystem.

---

# 9.10 Session Management

Sessions identify authenticated users.

Sessions should contain only the minimum required information.

Example

```text
User ID

Account Type

Authentication Status

Session Timestamp
```

Passwords should NEVER be stored inside sessions.

---

# 9.11 Session Security

Sessions should automatically terminate when:

- Logout
- Expiration
- Invalid Authentication
- Account Disabled

Every protected page must verify session validity before rendering.

---

# 9.12 Authentication Validation

Every authentication request should validate:

Registration

- Duplicate Number
- Invalid Format

Login

- Incorrect Password
- Nonexistent Account
- Disabled Account

OTP

- Invalid Code
- Expired Code

Password Reset

- Verified OTP
- Matching Passwords

Validation exists on both:

Client

AND

Server

Server validation is authoritative.

---

# 9.13 Authentication Security Rules

Passwords

Must be hashed using:

```
password_hash()
```

Verification

Must use:

```
password_verify()
```

Never:

Store plaintext passwords.

Display passwords.

Transmit passwords unnecessarily.

---

# 9.14 Account Status

Customer accounts should support statuses.

Examples

```text
Pending Verification

Active

Disabled

Suspended

Archived
```

Inactive accounts must not authenticate.

---

# 9.15 Activity Logging

Authentication events generate Activity Logs.

Examples

- Registration
- Login
- Logout
- Password Change
- Password Reset
- Failed Login
- Failed OTP

---

# 9.16 Timeline Integration

Customer Timeline should record:

Registered

↓

OTP Verified

↓

Profile Completed

↓

First Login

↓

Password Changed

↓

Password Reset

Timeline entries are customer-facing.

---

# 9.17 Administrator Authentication

Administrator authentication remains independent.

Administrator accounts:

- Are stored separately.
- Use separate permissions.
- Use separate dashboards.
- Cannot authenticate as customers.

Customer accounts cannot access administrator pages.

Administrator accounts cannot access customer dashboards.

---

# 9.18 Filesystem Ownership

```text
auth/

register.php

login.php

logout.php

verify_otp.php

forgot_password.php

reset_password.php
```

Helper Ownership

```text
helpers/

auth_helper.php

validation_helper.php

session_helper.php
```

Configuration Ownership

```text
config/

session.php
```

Database Ownership

```text
users

otp_codes

sessions
```

---

# 9.19 Acceptance Criteria

Authentication is complete when:

✓ Registration works.

✓ Duplicate registrations are blocked.

✓ OTP verification works.

✓ Bootstrap OTP modal is implemented.

✓ Profile completion occurs after OTP verification.

✓ Login works.

✓ Logout destroys sessions.

✓ Password recovery works.

✓ Sessions are validated.

✓ Passwords are hashed.

✓ Activity Logs are generated.

✓ Timeline entries are generated.

✓ Administrator authentication remains isolated.

✓ No placeholder authentication logic exists.

---
END OF SECTION 5

# 10. CUSTOMER MODULE

## 10.1 Purpose

The Customer Module is responsible for managing the complete customer lifecycle.

It governs every interaction between a customer and the Lending Platform Framework, beginning with registration and ending with account archival.

This module is one of the core reusable components of the framework and must remain independent of any product-specific implementation.

---

# 10.2 Customer Lifecycle

Every customer follows the same lifecycle.

```text
Visitor

↓

Registration

↓

OTP Verification

↓

Profile Completion

↓

Credit Evaluation

↓

Eligible Customer

↓

Loan Application

↓

Loan Approval

↓

Active Borrower

↓

Loan Completion

↓

Repeat Customer
```

The framework should enforce this lifecycle consistently.

---

# 10.3 Customer Dashboard

After authentication, the customer is redirected to the Dashboard.

The Dashboard acts as the customer's central workspace.

The dashboard should present:

- Profile Summary
- Credit Status
- Available Credit Limit
- Active Loan
- Loan History
- Transaction History
- Timeline
- Notifications
- Quick Actions

The Dashboard must never expose administrator functionality.

---

# 10.4 Customer Profile

Every customer must maintain a complete profile.

Required information includes:

### Personal Information

- First Name
- Middle Name
- Last Name
- Suffix (optional)
- Birthdate
- Gender
- Civil Status
- Nationality

---

### Contact Information

- Mobile Number
- Email Address (optional)
- Residential Address

---

### Employment Information

- Employment Status
- Employer Name
- Occupation
- Monthly Income
- Length of Employment

---

### Emergency Contact

- Full Name
- Relationship
- Mobile Number

---

### Identification

Customer-uploaded documents:

- Valid Government ID
- Selfie
- Income Proof (if required)

---

# 10.5 Profile Completion Rules

Customers cannot proceed to credit evaluation until the profile is complete.

The system should validate required fields before allowing progression.

Incomplete profiles should display clear guidance indicating which information is still required.

---

# 10.6 Customer Status

Customers transition through predefined statuses.

```text
Pending Verification

↓

Verified

↓

Profile Incomplete

↓

Profile Complete

↓

Credit Evaluation

↓

Eligible

↓

Borrower

↓

Returning Customer

↓

Archived
```

Status changes should occur automatically when system conditions are met.

---

# 10.7 Customer Dashboard Components

The dashboard should include the following modules.

### Credit Summary

Displays:

- Current Credit Limit
- Available Credit
- Credit Status
- Credit Score Category

---

### Active Loan

Displays:

- Loan Amount
- Remaining Balance
- Due Date
- Current Status

---

### Loan History

Displays:

- Previous Loans
- Completed Loans
- Rejected Applications
- Pending Applications

---

### Timeline

Displays chronological customer events.

Examples:

- Registered
- OTP Verified
- Profile Completed
- Credit Evaluated
- Loan Submitted
- Loan Approved
- Payment Received

---

### Notifications

Displays:

- Loan Updates
- Payment Reminders
- Credit Updates
- System Announcements

---

### Quick Actions

Examples:

- Apply for Loan
- Update Profile
- Upload Documents
- View Transactions
- View Timeline

---

# 10.8 Customer Permissions

Customers may:

- View their own profile
- Update permitted profile fields
- Upload documents
- Submit loan applications
- View transactions
- View timeline
- View notifications

Customers may NOT:

- Access administrator pages
- View other customer data
- Modify system configuration
- Approve loans
- Modify credit calculations

---

# 10.9 Customer Validation

Before any protected operation, verify:

- Customer is authenticated
- Customer account is active
- OTP verification completed
- Profile completed (where required)
- Required documents uploaded (where applicable)

Validation failures should prevent continuation and present meaningful feedback.

---

# 10.10 Customer Activity Logging

The following customer actions generate Activity Logs:

- Registration
- Login
- Logout
- Profile Update
- Document Upload
- Loan Submission
- Password Change
- Password Reset

Activity Logs are intended for administrators and auditing.

---

# 10.11 Customer Timeline

The following customer actions generate Timeline Events:

- Registered
- OTP Verified
- Profile Completed
- Credit Evaluated
- Loan Submitted
- Loan Approved
- Loan Rejected
- Loan Released
- Payment Recorded
- Loan Completed

Timeline entries are visible to the customer.

---

# 10.12 Customer Document Management

Customers may upload required documents through the dashboard.

Supported document categories include:

```text
Valid ID

Selfie

Proof of Income

Supporting Documents
```

Rules:

- Validate file type.
- Validate maximum file size.
- Store files in `/uploads/`.
- Store metadata in the database.
- Prevent executable file uploads.

---

# 10.13 Customer Module Ownership

Filesystem

```text
pages/

dashboard.php

profile.php

loan.php

loan_history.php

transactions.php

timeline.php

notifications.php

settings.php
```

Helpers

```text
helpers/

customer_helper.php

profile_helper.php

upload_helper.php
```

Database

```text
users

customer_profiles

uploaded_documents

timeline

notifications

activity_logs
```

---

# 10.14 Acceptance Criteria

The Customer Module is complete when:

✓ Customer lifecycle is fully implemented.

✓ Profile completion is enforced.

✓ Dashboard displays required information.

✓ Customer permissions are enforced.

✓ Document uploads are validated.

✓ Timeline entries are generated.

✓ Activity Logs are generated.

✓ Customer data remains isolated.

✓ Administrator functionality is inaccessible to customers.

✓ No duplicate customer workflows exist.

---
END OF SECTION 6

# 11. CREDIT EVALUATION ENGINE

## 11.1 Purpose

The Credit Evaluation Engine is responsible for determining whether a customer is financially eligible to borrow and, if eligible, calculating an appropriate credit limit.

This module operates independently from the Loan Engine.

The Credit Engine determines **whether a customer may borrow**.

The Loan Engine determines **how a loan is processed**.

This separation must always be maintained.

---

# 11.2 Objectives

The Credit Evaluation Engine shall:

- Evaluate customer eligibility.
- Analyze submitted information.
- Calculate customer risk.
- Assign a credit score.
- Determine a recommended credit limit.
- Store evaluation history.
- Produce administrator recommendations.

The engine must never approve or reject loans directly.

---

# 11.3 Credit Evaluation Workflow

```text
Customer Profile Complete

↓

Required Documents Uploaded

↓

Credit Evaluation Requested

↓

Validation

↓

Risk Assessment

↓

Credit Score Calculation

↓

Credit Limit Recommendation

↓

Store Evaluation

↓

Customer Becomes Eligible
```

Loan applications may only proceed after a successful credit evaluation.

---

# 11.4 Evaluation Requirements

Before evaluation begins, verify:

✓ Customer account is active.

✓ OTP verification completed.

✓ Customer profile completed.

✓ Required documents uploaded.

✓ Customer is not currently under review.

If any requirement fails, evaluation must stop.

---

# 11.5 Evaluation Factors

The framework should support evaluation using multiple criteria.

Examples include:

### Identity Verification

- Government ID
- Selfie Verification
- Document Completeness

---

### Employment

- Employment Status
- Employer
- Occupation
- Employment Duration

---

### Income

- Monthly Income
- Income Stability

---

### Existing Loan History

- Previous Loans
- Repayment History
- Outstanding Balances

---

### Customer History

- Timeline Events
- Previous Evaluations
- Platform Activity

The framework should allow future expansion of evaluation criteria without redesigning the module.

---

# 11.6 Credit Score

Every evaluation produces a credit score.

The scoring algorithm should remain centralized inside the Credit Engine.

No other module may calculate credit independently.

The score is used internally to determine eligibility and credit limits.

The exact scoring formula should remain configurable and must not be hardcoded throughout the application.

---

# 11.7 Risk Categories

Customers should be categorized according to evaluation results.

Example categories:

```text
Very Low Risk

Low Risk

Moderate Risk

High Risk

Very High Risk
```

Risk categories assist administrators but do not automatically approve or reject applications.

---

# 11.8 Credit Limit

Following evaluation, the system assigns a credit limit.

The credit limit represents the maximum amount a customer may borrow.

The available credit decreases as active loans consume the limit and increases again as loans are repaid.

Credit limits should be recalculated only through the Credit Engine.

---

# 11.9 Credit Status

Customers should maintain a current credit status.

Example statuses:

```text
Not Evaluated

Under Evaluation

Eligible

Limited Eligibility

Ineligible

Suspended
```

Status changes should occur automatically based on evaluation outcomes and administrator actions where applicable.

---

# 11.10 Credit History

Every evaluation should be preserved.

Historical evaluations provide:

- Previous Scores
- Previous Limits
- Previous Risk Categories
- Evaluation Dates

The system should never overwrite historical evaluations.

Instead, create a new evaluation record.

---

# 11.11 Administrator Interaction

Administrators may:

- Review evaluations.
- Trigger reevaluation.
- View supporting information.
- Override limits (if authorized).
- Add evaluation remarks.

Administrators should not modify calculated values directly unless their role explicitly permits overrides.

---

# 11.12 Customer Interaction

Customers may:

- View current credit limit.
- View eligibility status.
- View evaluation date.
- View general recommendations.

Customers should NOT see internal scoring formulas or administrator-only remarks.

---

# 11.13 Reevaluation

Customers may be reevaluated when:

- Profile information changes.
- Employment changes.
- Income changes.
- Administrator requests reevaluation.
- Framework policy requires periodic reevaluation.

Reevaluation should create a new evaluation record without deleting previous records.

---

# 11.14 Activity Logging

The following actions generate Activity Logs:

- Credit Evaluation Requested
- Evaluation Completed
- Credit Limit Updated
- Administrator Override
- Reevaluation Requested

Activity Logs are intended for administrators and auditing.

---

# 11.15 Timeline Events

The following customer-visible Timeline events should be generated:

- Credit Evaluation Started
- Credit Evaluation Completed
- Credit Limit Assigned
- Credit Limit Updated
- Reevaluation Completed

---

# 11.16 Module Ownership

Filesystem

```text
pages/

credit.php

credit_status.php
```

Helpers

```text
helpers/

credit_helper.php

risk_helper.php
```

Database

```text
credit_evaluations

credit_limits

timeline

activity_logs
```

---

# 11.17 Acceptance Criteria

The Credit Evaluation Engine is complete when:

✓ Eligibility validation is enforced.

✓ Evaluation factors are processed.

✓ Credit scores are generated.

✓ Risk categories are assigned.

✓ Credit limits are calculated.

✓ Historical evaluations are preserved.

✓ Reevaluation is supported.

✓ Timeline events are generated.

✓ Activity Logs are generated.

✓ Credit calculations remain centralized.

✓ Loan Engine does not duplicate credit calculations.

---
END OF SECTION 7

# 12. LOAN PROCESSING ENGINE

## 12.1 Purpose

The Loan Processing Engine is responsible for managing the complete lifecycle of every loan issued by the platform.

Unlike the Credit Evaluation Engine, which determines borrowing eligibility, the Loan Processing Engine manages the loan itself from application through repayment and closure.

This module must remain completely reusable and independent of any AuraPay-specific implementation.

---

# 12.2 Objectives

The Loan Processing Engine shall:

- Accept loan applications.
- Validate eligibility.
- Calculate loan details.
- Manage approval workflow.
- Release approved loans.
- Monitor repayments.
- Update balances.
- Close completed loans.
- Generate timeline events.
- Generate activity logs.

---

# 12.3 Loan Lifecycle

Every loan follows the same lifecycle.

```text
Eligible Customer

↓

Loan Application

↓

System Validation

↓

Administrator Review

↓

Approved / Rejected

↓

Loan Disbursement

↓

Active Loan

↓

Repayments

↓

Loan Completed

↓

Loan Archived
```

This lifecycle is mandatory for every lending product built using the framework.

---

# 12.4 Loan Application Requirements

Before a customer may submit a loan application, verify:

✓ Customer authenticated.

✓ OTP verified.

✓ Profile completed.

✓ Credit evaluation completed.

✓ Customer is eligible.

✓ Customer has available credit.

✓ Customer has no blocking restrictions.

If any requirement fails, the application must not proceed.

---

# 12.5 Loan Application

The customer provides:

- Loan Amount
- Loan Purpose
- Preferred Term (if applicable)

The system validates:

- Requested amount.
- Available credit.
- Minimum loan amount.
- Maximum loan amount.
- Customer eligibility.

Successful validation creates a new loan application.

---

# 12.6 Loan Status

Loan Applications support:

```text
Draft

Submitted

Under Review

Approved

Rejected

Cancelled
```

Approved applications become Loans.

Rejected applications remain historical records.

Applications should never be permanently deleted.

---

# 12.7 Loan Approval Workflow

```text
Application Submitted

↓

Administrator Review

↓

Decision

↓

Approved

or

Rejected
```

Approval is performed only by authorized administrators.

No automatic approval should occur unless explicitly configured by the framework.

---

# 12.8 Loan Generation

Upon approval, the framework creates an active loan.

The loan contains:

- Principal Amount
- Interest
- Processing Fee
- Total Repayable Amount
- Remaining Balance
- Release Date
- Due Date
- Current Status

The original application remains preserved.

---

# 12.9 Loan Calculations

The Loan Engine is responsible for calculating:

- Interest
- Processing Fee
- Total Amount
- Remaining Balance

Calculation values should originate from the Configuration System.

Never hardcode:

- Interest Rate
- Processing Fee
- Loan Limits

These values must remain configurable.

---

# 12.10 Loan Disbursement

After approval:

Loan

↓

Disbursement

↓

Transaction Record

↓

Timeline Event

↓

Activity Log

↓

Customer Notification

Disbursement should occur only once per approved loan.

---

# 12.11 Active Loan

An active loan displays:

- Original Amount
- Remaining Balance
- Amount Paid
- Next Due Date
- Loan Status

Customers should be able to monitor repayment progress through the dashboard.

---

# 12.12 Repayment

Each payment updates:

- Remaining Balance
- Payment History
- Timeline
- Activity Logs
- Transactions

Repayments should never overwrite historical records.

Each payment becomes its own record.

---

# 12.13 Loan Completion

A loan becomes completed when:

Remaining Balance

=

0

Completion performs:

- Loan Status Update
- Timeline Event
- Activity Log
- Customer Notification
- Credit Availability Update

---

# 12.14 Credit Synchronization

When loans become active:

Available Credit

↓

Decreases

When loans are completed:

Available Credit

↓

Increases

The Loan Engine should coordinate with the Credit Engine but must never calculate credit independently.

---

# 12.15 Loan History

Customers may view:

- Pending Applications
- Approved Loans
- Active Loans
- Completed Loans
- Rejected Applications

History must remain permanent for auditing purposes.

---

# 12.16 Loan Restrictions

Customers should not:

- Exceed available credit.
- Submit invalid loan amounts.
- Modify approved loans.
- Modify completed loans.
- Delete historical applications.

---

# 12.17 Administrator Functions

Authorized administrators may:

- Review applications.
- Approve applications.
- Reject applications.
- View repayment history.
- View loan history.
- Monitor balances.
- Add administrative remarks.

Administrators should not directly manipulate repayment balances outside documented administrative procedures.

---

# 12.18 Timeline Events

Loan actions generate customer Timeline events.

Examples:

- Loan Application Submitted
- Loan Under Review
- Loan Approved
- Loan Rejected
- Loan Released
- Payment Received
- Loan Completed

---

# 12.19 Activity Logs

Loan actions generate administrator Activity Logs.

Examples:

- Application Created
- Application Approved
- Application Rejected
- Loan Released
- Payment Recorded
- Loan Completed
- Administrator Review

---

# 12.20 Module Ownership

Filesystem

```text
pages/

loan.php

loan_history.php

loan_details.php
```

Helpers

```text
helpers/

loan_helper.php

payment_helper.php

transaction_helper.php
```

Database

```text
loan_applications

loans

loan_payments

transactions

timeline

activity_logs
```

---

# 12.21 Acceptance Criteria

The Loan Processing Engine is complete when:

✓ Loan applications are validated.

✓ Eligibility is enforced.

✓ Administrator approval workflow functions correctly.

✓ Loans are generated from approved applications.

✓ Loan calculations are centralized.

✓ Repayments update balances correctly.

✓ Loan completion restores available credit.

✓ Timeline events are generated.

✓ Activity Logs are generated.

✓ Loan history remains permanent.

✓ Credit calculations remain the responsibility of the Credit Engine.

✓ No duplicate loan workflows exist.

---
END OF SECTION 8

# 13. TIMELINE & ACTIVITY LOG SYSTEM

## 13.1 Purpose

The Timeline & Activity Log System records everything that happens inside the Lending Platform Framework.

Although these systems are closely related, they serve different audiences and must remain completely independent.

The Timeline is customer-facing.

The Activity Log is administrator-facing.

They must never be merged into a single implementation.

---

# 13.2 Core Philosophy

Every significant action performed within the framework should generate a permanent historical record.

No important event should disappear after it occurs.

The framework should provide complete traceability for:

- Customers
- Administrators
- Auditing
- Troubleshooting
- Compliance
- Customer transparency

---

# 13.3 Timeline vs Activity Logs

## Timeline

Audience

Customer

Purpose

Displays important milestones in the customer's journey.

Examples

- Registration
- OTP Verified
- Profile Completed
- Credit Evaluated
- Loan Submitted
- Loan Approved
- Loan Released
- Payment Recorded
- Loan Completed

Timeline should be easy to read and chronological.

---

## Activity Logs

Audience

Administrator

Purpose

Records every significant system action for auditing and monitoring.

Examples

- Administrator Login
- Customer Login
- Failed Login
- Password Change
- Loan Approval
- Loan Rejection
- Configuration Change
- Document Upload
- Credit Override

Activity Logs are administrative records and should not be visible to customers.

---

# 13.4 Timeline Workflow

Every customer event follows the same workflow.

```text
Customer Action

↓

Business Logic

↓

Timeline Event Created

↓

Stored in Database

↓

Displayed Chronologically
```

Timeline events should be immutable.

They should never be modified after creation.

---

# 13.5 Activity Log Workflow

Every administrative or security-sensitive action follows this workflow.

```text
System Action

↓

Activity Log Generated

↓

Stored Permanently

↓

Administrator Review
```

Activity Logs should support future reporting and auditing.

---

# 13.6 Timeline Event Categories

The framework should support standardized timeline categories.

## Account

Examples

- Account Created
- OTP Verified
- Profile Completed

---

## Credit

Examples

- Credit Evaluation Started
- Credit Evaluation Completed
- Credit Limit Updated

---

## Loan

Examples

- Application Submitted
- Application Approved
- Application Rejected
- Loan Released
- Loan Completed

---

## Payments

Examples

- Payment Received
- Balance Updated

---

## Notifications

Examples

- Reminder Sent
- System Notification Delivered

---

# 13.7 Activity Categories

The framework should categorize Activity Logs.

## Authentication

- Login
- Logout
- Failed Login
- Password Reset

---

## Customer

- Profile Updated
- Documents Uploaded
- Loan Submitted

---

## Administrator

- Loan Approved
- Loan Rejected
- Customer Updated
- Credit Override

---

## Configuration

- Interest Updated
- Processing Fee Changed
- Maintenance Enabled
- Maintenance Disabled

---

## Security

- Unauthorized Access Attempt
- Invalid Session
- Permission Denied

---

# 13.8 Timeline Rules

Timeline entries must:

✓ Be chronological.

✓ Be customer-readable.

✓ Never expose internal system information.

✓ Never expose administrator remarks.

✓ Never expose sensitive data.

Timeline entries should be concise and understandable.

---

# 13.9 Activity Log Rules

Activity Logs must:

✓ Record precise timestamps.

✓ Record actor identity.

✓ Record action performed.

✓ Record affected resource.

✓ Record result.

Activity Logs should prioritize auditing rather than readability.

---

# 13.10 Event Generation Policy

Timeline events should be generated automatically.

Developers should never manually insert Timeline records from presentation pages.

Instead:

Customer Action

↓

Helper

↓

Timeline Helper

↓

Database

The same rule applies to Activity Logs.

---

# 13.11 Timestamp Policy

Every Timeline Event stores:

- Event Time
- Event Type
- Customer ID

Every Activity Log stores:

- Timestamp
- User
- Role
- Action
- Target
- Result

---

# 13.12 Immutability

Historical records should never be edited.

If a correction is required,

create a new event.

Never overwrite history.

This principle preserves audit integrity.

---

# 13.13 Customer Dashboard Integration

Customer Dashboard should display:

Recent Timeline

Examples

```text
✓ Registered

✓ OTP Verified

✓ Credit Evaluated

✓ Loan Submitted

✓ Loan Approved

✓ Payment Received
```

The Timeline becomes part of the customer's experience.

---

# 13.14 Administrator Dashboard Integration

Administrator Dashboard should provide:

- Activity Viewer
- Filters
- Search
- Date Range
- User Search
- Action Search

Administrators should be able to investigate platform activity efficiently.

---

# 13.15 Module Ownership

Filesystem

```text
pages/

timeline.php

notifications.php

admin/

activity_logs.php

timeline.php
```

Helpers

```text
helpers/

timeline_helper.php

activity_helper.php
```

Database

```text
timeline

activity_logs
```

---

# 13.16 Acceptance Criteria

Timeline & Activity Logging is complete when:

✓ Timeline and Activity Logs remain separate.

✓ Timeline events are customer-facing.

✓ Activity Logs are administrator-facing.

✓ Events are generated automatically.

✓ Historical records remain immutable.

✓ Dashboard integration is complete.

✓ Filtering and auditing are supported.

✓ No duplicate logging systems exist.

---
END OF SECTION 9

# 14. ADMINISTRATOR MODULE

## 14.1 Purpose

The Administrator Module is responsible for managing, monitoring, and controlling the entire Lending Platform Framework.

Unlike the Customer Module, which serves individual borrowers, the Administrator Module serves internal platform operations.

Every administrative action must be authenticated, authorized, logged, and auditable.

---

# 14.2 Objectives

The Administrator Module shall provide:

- Secure Administrator Authentication
- Administrator Dashboard
- Customer Management
- Loan Management
- Credit Management
- Transaction Monitoring
- Reports
- Activity Monitoring
- Platform Configuration
- System Maintenance

The Administrator Module is reusable and must remain independent from the AuraPay Product Layer.

---

# 14.3 Administrator Architecture

```text
Administrator Login

↓

Authentication

↓

Permission Validation

↓

Administrator Dashboard

↓

Administrative Modules
```

Administrator functionality must remain isolated from customer functionality.

---

# 14.4 Administrator Dashboard

The Administrator Dashboard is the operational center of the platform.

It should present a summarized overview of:

- Total Customers
- Active Customers
- Pending Verifications
- Pending Loan Applications
- Active Loans
- Completed Loans
- Total Outstanding Balance
- Total Collections
- Recent Activity
- System Notifications

The dashboard should prioritize operational awareness.

---

# 14.5 Administrator Roles

The framework supports role-based access control.

Example roles:

```text
Super Administrator

Loan Officer

Finance Officer

Customer Support

Compliance Officer
```

Every administrator account belongs to exactly one role.

Future versions may support multiple roles if required.

---

# 14.6 Permission System

Permissions should determine access to modules.

Examples:

### Customer Management

- View Customers
- Edit Customers
- Disable Customers

---

### Credit

- View Credit
- Trigger Reevaluation
- Override Credit Limit

---

### Loans

- Review Applications
- Approve Loans
- Reject Loans
- Release Loans

---

### Reports

- View Reports
- Export Reports

---

### Configuration

- Modify Settings
- Update Interest
- Update Fees
- Maintenance Mode

Permissions should never be hardcoded into presentation pages.

Authorization must occur server-side.

---

# 14.7 Customer Management

Administrators may:

- Search Customers
- View Profiles
- Review Documents
- View Timeline
- View Loan History
- View Credit Status

Administrators should not directly modify customer financial history unless specifically authorized.

---

# 14.8 Loan Management

Administrators may:

- Review Applications
- Approve Loans
- Reject Loans
- View Active Loans
- Monitor Repayments
- Close Loans (when appropriate)

Loan management should always generate Timeline Events and Activity Logs.

---

# 14.9 Credit Management

Administrators may:

- View Credit Evaluations
- Trigger Reevaluation
- View Risk Categories
- View Credit Limits

If administrator overrides are enabled by framework policy:

- Every override must be logged.
- Every override must record the administrator responsible.
- Every override should preserve the previous value.

---

# 14.10 Transaction Monitoring

Administrators should monitor:

- Loan Disbursements
- Customer Payments
- Outstanding Balances
- Financial Activity

Transactions are read-only historical records.

They should never be modified directly.

---

# 14.11 Reports

The framework should support reporting for:

### Customers

- Registered Customers
- Active Customers
- Inactive Customers

---

### Loans

- Pending Applications
- Approved Loans
- Active Loans
- Completed Loans
- Rejected Loans

---

### Financial

- Collections
- Outstanding Loans
- Released Funds

---

### Platform

- Daily Activity
- Administrator Activity
- Customer Activity

Reports should support future export functionality.

---

# 14.12 Platform Configuration

Administrators manage configurable platform values.

Examples:

- Interest Rate
- Processing Fee
- Minimum Loan
- Maximum Loan
- OTP Expiration
- Upload Limits
- Maintenance Mode

Configuration values should always be loaded dynamically from the Configuration Module.

Never hardcode configurable values.

---

# 14.13 Maintenance Mode

The framework should support Maintenance Mode.

When enabled:

Customers

- Cannot access dashboards.
- Cannot submit loans.
- Cannot modify profiles.

Administrators

- Retain full access.

Maintenance Mode should display a configurable maintenance page.

---

# 14.14 Administrator Security

Administrator accounts require stronger protection.

Requirements:

- Hashed Passwords
- Session Validation
- Permission Validation
- Activity Logging
- Automatic Logout on Invalid Sessions

Every administrator page must verify:

- Authentication
- Role
- Permission

before rendering.

---

# 14.15 Administrator Activity Logs

Every administrative action must generate an Activity Log.

Examples:

- Login
- Logout
- Customer Viewed
- Loan Approved
- Loan Rejected
- Credit Override
- Configuration Updated
- Maintenance Enabled

Administrator activity should always be auditable.

---

# 14.16 Filesystem Ownership

Filesystem

```text
admin/

dashboard.php

customers.php

applications.php

loans.php

credit.php

transactions.php

reports.php

activity_logs.php

settings.php

maintenance.php

logout.php
```

Helpers

```text
helpers/

admin_helper.php

permission_helper.php

report_helper.php
```

Database

```text
administrators

roles

activity_logs

configuration
```

---

# 14.17 Acceptance Criteria

The Administrator Module is complete when:

✓ Administrator authentication is isolated.

✓ Role-based permissions are enforced.

✓ Customer management functions correctly.

✓ Loan management functions correctly.

✓ Credit management functions correctly.

✓ Reports are available.

✓ Configuration is dynamic.

✓ Maintenance Mode functions correctly.

✓ Administrative actions generate Activity Logs.

✓ No customer functionality is duplicated inside administrator modules.

---
END OF SECTION 10

# 15. CONFIGURATION SYSTEM

## 15.1 Purpose

The Configuration System centralizes every platform-wide setting used throughout the Lending Platform Framework.

Rather than hardcoding values inside PHP files, every configurable parameter should be managed from one location.

This design allows administrators to modify platform behavior without changing application source code.

---

# 15.2 Objectives

The Configuration System shall provide:

- Centralized platform settings
- Runtime configuration loading
- Administrator configuration management
- Future extensibility
- Consistent configuration access

Configuration values should always be treated as application data rather than source code.

---

# 15.3 Configuration Philosophy

No configurable value should be hardcoded across multiple files.

Incorrect:

```php
$interest = 15;
```

Correct:

```text
Configuration

↓

Database

↓

Configuration Helper

↓

Business Module
```

Every module should request values from the Configuration System.

---

# 15.4 Configuration Categories

## Financial Configuration

Examples:

- Interest Rate
- Processing Fee
- Late Payment Fee
- Minimum Loan
- Maximum Loan
- Credit Limit Ceiling

---

## Authentication Configuration

Examples:

- OTP Expiration
- Session Timeout
- Password Requirements
- Login Attempts

---

## Upload Configuration

Examples:

- Maximum File Size
- Allowed Extensions
- Allowed MIME Types
- Upload Directory

---

## Loan Configuration

Examples:

- Default Loan Term
- Maximum Loan Term
- Grace Period
- Repayment Frequency

---

## Platform Configuration

Examples:

- Platform Name
- Company Name
- Maintenance Mode
- Contact Information
- Customer Support Details

---

## Notification Configuration

Examples:

- Reminder Interval
- Notification Retention
- Default Notification Types

---

# 15.5 Configuration Storage

Configuration should be stored in:

```text
Database

↓

configuration
```

Each configuration entry should contain:

- Configuration Key
- Configuration Value
- Description
- Last Updated
- Updated By

This allows future auditing and administrative tracking.

---

# 15.6 Configuration Loading

Application Startup

↓

Configuration Helper

↓

Load Configuration

↓

Cache Values

↓

Application Modules

Configuration should be loaded once and reused during the request lifecycle whenever possible.

---

# 15.7 Configuration Helper

All configuration access must pass through:

```text
helpers/

configuration_helper.php
```

No module should directly query configuration values from the database.

The helper serves as the single access point.

---

# 15.8 Administrator Configuration

Authorized administrators may:

- View Configuration
- Update Configuration
- Restore Defaults (if implemented)

Configuration changes should immediately affect new requests without requiring application code changes.

---

# 15.9 Configuration Security

Only authorized administrators may modify configuration.

Configuration updates should require:

- Authentication
- Permission Validation
- Activity Logging

Every configuration modification must record:

- Administrator
- Timestamp
- Previous Value
- New Value

---

# 15.10 Maintenance Mode

Maintenance Mode is managed through the Configuration System.

When enabled:

Customers

- Landing page may remain accessible.
- Dashboard access denied.
- Loan applications disabled.

Administrators

- Full access retained.

The maintenance page should be configurable.

---

# 15.11 Future Expandability

The Configuration System should support future modules without redesign.

Examples:

- SMS Gateway
- Email Gateway
- Payment Gateway
- Insurance Module
- Rewards System

Future configuration should simply introduce new keys rather than modifying framework architecture.

---

# 15.12 Module Ownership

Filesystem

```text
config/

app.php

constants.php

database.php

session.php
```

Helpers

```text
helpers/

configuration_helper.php
```

Database

```text
configuration
```

---

# 15.13 Acceptance Criteria

The Configuration System is complete when:

✓ Platform values are centralized.

✓ Configuration is database-driven.

✓ Configuration Helper is the single access point.

✓ Hardcoded business values are eliminated.

✓ Administrator configuration management functions correctly.

✓ Configuration changes generate Activity Logs.

✓ Maintenance Mode is configurable.

✓ Future modules can introduce configuration without redesign.

---

# 16. HELPER LIBRARY

## 16.1 Purpose

The Helper Library contains reusable business logic shared across multiple modules.

Helpers exist to eliminate duplicated code and centralize framework behavior.

Business logic should be written once and reused everywhere.

---

# 16.2 Philosophy

Helpers should contain:

- Business Logic
- Utility Functions
- Validation
- Shared Processes

Helpers should NOT contain:

- HTML
- Bootstrap
- SQL Views
- Presentation Logic

---

# 16.3 Helper Architecture

Presentation Layer

↓

Business Module

↓

Helper Library

↓

Database

Presentation should never bypass helpers.

---

# 16.4 Standard Helpers

The framework includes the following helper modules.

---

## Authentication Helper

Responsibilities

- Login Validation
- Registration
- Password Hashing
- Session Validation
- Authentication Checks

---

## Customer Helper

Responsibilities

- Customer Retrieval
- Customer Updates
- Customer Status

---

## Profile Helper

Responsibilities

- Profile Completion
- Profile Validation
- Profile Updates

---

## Credit Helper

Responsibilities

- Credit Evaluation
- Credit Status
- Credit Limit Retrieval

Credit calculations should remain centralized here.

---

## Risk Helper

Responsibilities

- Risk Classification
- Risk Evaluation
- Score Interpretation

---

## Loan Helper

Responsibilities

- Loan Creation
- Loan Status
- Loan Validation
- Loan Calculations

---

## Payment Helper

Responsibilities

- Payment Recording
- Remaining Balance
- Payment Validation

---

## Transaction Helper

Responsibilities

- Transaction History
- Financial Records

---

## Timeline Helper

Responsibilities

- Timeline Generation
- Timeline Retrieval

---

## Activity Helper

Responsibilities

- Activity Log Generation
- Activity Retrieval

---

## Upload Helper

Responsibilities

- File Validation
- File Storage
- Upload Security

---

## Notification Helper

Responsibilities

- Notification Creation
- Notification Retrieval

---

## Validation Helper

Responsibilities

- Input Validation
- Required Fields
- Business Validation

---

## Configuration Helper

Responsibilities

- Configuration Retrieval
- Cached Configuration Access

---

## Permission Helper

Responsibilities

- Role Validation
- Permission Checks

---

## Report Helper

Responsibilities

- Report Generation
- Summary Calculations

---

# 16.5 Helper Rules

Helpers should:

✓ Perform one responsibility.

✓ Remain reusable.

✓ Remain framework-independent.

✓ Avoid duplicated functionality.

Helpers should never call presentation pages.

---

# 16.6 Helper Ownership

Filesystem

```text
helpers/

auth_helper.php

customer_helper.php

profile_helper.php

credit_helper.php

risk_helper.php

loan_helper.php

payment_helper.php

transaction_helper.php

timeline_helper.php

activity_helper.php

upload_helper.php

notification_helper.php

validation_helper.php

configuration_helper.php

permission_helper.php

report_helper.php
```

---

# 16.7 Acceptance Criteria

The Helper Library is complete when:

✓ Business logic is centralized.

✓ Modules reuse helper functionality.

✓ No duplicated business calculations exist.

✓ Presentation never bypasses helpers.

✓ Every helper has a single responsibility.

✓ Future modules can introduce additional helpers without affecting existing architecture.

---
END OF SECTION 11

# 17. SECURITY MODEL

## 17.1 Purpose

The Security Model defines the mandatory security standards implemented throughout the Lending Platform Framework.

Security is not a separate module.

It is a cross-cutting responsibility applied to every layer of the system.

Every module must comply with this section.

---

# 17.2 Security Philosophy

The framework follows the principle of:

**Deny by Default.**

Unless explicitly permitted,

access should be denied.

Authentication alone is not authorization.

Every protected action must verify both:

- Authentication
- Authorization

---

# 17.3 Security Layers

The framework protects:

```text
Presentation Layer

↓

Authentication

↓

Authorization

↓

Business Logic Validation

↓

Database Validation

↓

Data Storage
```

Every layer contributes to platform security.

---

# 17.4 Authentication Security

Authentication requires:

✓ Password Hashing

✓ Secure Sessions

✓ OTP Verification

✓ Account Validation

✓ Session Expiration

Passwords must always use:

```php
password_hash()

password_verify()
```

Never implement custom password encryption.

---

# 17.5 Authorization

Authorization determines what an authenticated user may access.

Customers

↓

Customer Modules

Administrators

↓

Administrator Modules

Role Permissions

↓

Specific Administrative Features

Permission checks must always occur server-side.

---

# 17.6 Session Security

Sessions should:

- Start after successful authentication.
- Expire automatically.
- Be destroyed on logout.
- Be validated on every protected page.

Invalid sessions should immediately redirect to authentication.

---

# 17.7 Input Validation

Every user input must be validated.

Validation exists in two layers.

Client

↓

Convenience

Server

↓

Authority

Server validation is always mandatory.

Never trust browser input.

---

# 17.8 SQL Security

Database access should use:

Prepared Statements

Parameterized Queries

Never:

```sql
SELECT * FROM users
WHERE id = $_GET['id']
```

Direct query concatenation is prohibited.

SQL Injection protection is mandatory.

---

# 17.9 XSS Protection

All user-generated content should be escaped before rendering.

Examples:

- Customer Names
- Addresses
- Administrator Remarks
- Loan Purpose
- Notifications

Never render raw HTML originating from customer input.

---

# 17.10 CSRF Protection

Sensitive operations should include CSRF protection.

Examples:

- Login
- Registration
- Profile Update
- Loan Submission
- Configuration Changes
- Administrator Actions

State-changing operations should never rely solely on POST requests.

---

# 17.11 File Upload Security

Uploaded files require validation.

Validate:

✓ Extension

✓ MIME Type

✓ File Size

✓ Upload Errors

Uploaded files should never execute.

Uploads remain inside:

```text
uploads/
```

Uploads should never be publicly executable.

---

# 17.12 Password Policy

Passwords should:

- Meet minimum length requirements.
- Be hashed.
- Never be stored in plaintext.
- Never be logged.
- Never be displayed.

Password reset should always require OTP verification.

---

# 17.13 Account Locking

The framework should support future implementation of:

- Login Attempt Limits
- Temporary Lockouts
- Administrator Unlock

This behavior should remain configurable.

---

# 17.14 Permission Validation

Every administrator page verifies:

Authentication

↓

Role

↓

Permission

↓

Requested Action

Failure should immediately terminate the request.

---

# 17.15 Sensitive Information

Sensitive information includes:

- Passwords
- Session IDs
- OTP Codes
- Internal Configuration
- Administrator Credentials

Sensitive information must never appear in:

- Logs
- URLs
- Public HTML
- JavaScript Variables

---

# 17.16 Audit Trail

Administrative actions should remain auditable.

Examples:

- Loan Approval
- Credit Override
- Configuration Update
- Customer Suspension

Every audit entry should identify:

- Who
- What
- When

---

# 17.17 Error Handling

The framework should never expose:

- SQL Errors
- Stack Traces
- PHP Warnings
- Internal Paths
- Configuration Values

Public error messages should remain generic.

Detailed errors belong only in development environments.

---

# 17.18 Security Ownership

Authentication

↓

Authentication Module

Permissions

↓

Permission Helper

Uploads

↓

Upload Helper

Sessions

↓

Configuration

Activity Logs

↓

Activity Helper

---

# 17.19 Acceptance Criteria

Security implementation is complete when:

✓ Passwords are hashed.

✓ Sessions are validated.

✓ Permissions are enforced.

✓ SQL Injection is prevented.

✓ XSS is prevented.

✓ CSRF protection is implemented.

✓ Upload validation is enforced.

✓ Administrator actions are audited.

✓ Sensitive information remains protected.

---

# 18. BUSINESS RULES

## 18.1 Purpose

Business Rules define how the Lending Platform behaves.

Unlike source code,

Business Rules describe platform behavior.

All modules should reference these rules.

Business Rules should never be duplicated throughout the framework.

---

# 18.2 Core Principles

The framework enforces:

- One customer account per mobile number.
- One active authentication session per login.
- Credit evaluation before borrowing.
- Administrator approval before loan release.
- Historical data preservation.
- Immutable timeline history.

---

# 18.3 Registration Rules

A customer:

Must:

✓ Verify OTP

✓ Complete Profile

Before:

Credit Evaluation

Registration is incomplete until both requirements are satisfied.

---

# 18.4 Credit Rules

Customers cannot borrow until:

✓ Credit Evaluation Completed

AND

✓ Eligible Status

Credit calculations remain exclusively inside the Credit Engine.

---

# 18.5 Loan Rules

Customers may only borrow:

Within Available Credit.

Applications exceeding available credit must be rejected.

Loan approval remains an administrative decision unless future configuration enables automatic approval.

---

# 18.6 Repayment Rules

Payments:

- Reduce Remaining Balance.
- Generate Timeline Events.
- Generate Activity Logs.
- Create Transaction Records.

Historical payments must never be modified.

---

# 18.7 Customer Rules

Customers may:

- Update profile.
- Upload documents.
- Apply for loans.
- View history.

Customers may not:

- Approve loans.
- Modify credit limits.
- Change configuration.
- Access administrator modules.

---

# 18.8 Administrator Rules

Administrators may:

- Review applications.
- Approve loans.
- Reject loans.
- Manage customers.
- Configure the platform.

Every administrative action should be logged.

---

# 18.9 Timeline Rules

Every significant customer milestone should generate a Timeline Event.

Timeline entries should remain permanent.

Timeline entries should never be edited.

---

# 18.10 Activity Log Rules

Every security-sensitive or administrative action should generate an Activity Log.

Logs remain permanent.

Logs support future auditing.

---

# 18.11 Configuration Rules

Business values should originate from the Configuration System.

Never hardcode:

- Interest
- Fees
- Limits
- Expiration Times

Modules should request values through the Configuration Helper.

---

# 18.12 Product Rules

The Lending Platform Framework remains reusable.

AuraPay defines only:

- Branding
- Landing Page
- Theme
- Product Assets
- Marketing

Changing products must not require modifying the Framework.

---

# 18.13 Acceptance Criteria

Business Rules are complete when:

✓ Customer lifecycle is enforced.

✓ Credit precedes borrowing.

✓ Loan workflow remains standardized.

✓ Timeline remains immutable.

✓ Activity Logs remain permanent.

✓ Product customization remains isolated.

✓ Framework behavior is consistent across future products.

---
END OF SECTION 12

# 19. UI DESIGN SYSTEM

## 19.1 Purpose

The UI Design System defines the visual and interaction standards used throughout the Lending Platform Framework.

The objective is consistency.

Every page should feel like part of the same application.

The UI should prioritize:

- Simplicity
- Clarity
- Accessibility
- Mobile Responsiveness
- Professional Appearance

---

# 19.2 Design Philosophy

The framework follows these principles:

- Minimalistic
- Professional
- Financial Industry Appropriate
- Mobile First
- Responsive
- Consistent

Fancy animations and unnecessary visual effects should never compromise usability.

---

# 19.3 Framework Separation

The Framework defines:

- Components
- Layouts
- Navigation
- Forms
- Tables
- Cards
- Responsive Rules

AuraPay defines:

- Colors
- Branding
- Logos
- Marketing Graphics
- Landing Page Theme

This allows future lending products to reuse the UI system while replacing only product branding.

---

# 19.4 Responsive Design

The platform must be fully responsive.

Primary target:

```text
Mobile Devices
```

Secondary targets:

- Tablet
- Desktop

Layouts should never require horizontal scrolling.

---

# 19.5 Standard Layout

Every authenticated page follows the same structure.

```text
Header

↓

Navigation

↓

Page Content

↓

Cards / Tables / Forms

↓

Footer
```

Layout consistency improves usability.

---

# 19.6 Navigation

Customer Navigation

Examples

- Dashboard
- Credit
- Loan
- Transactions
- Timeline
- Notifications
- Settings

Administrator Navigation

Examples

- Dashboard
- Customers
- Applications
- Loans
- Credit
- Reports
- Settings
- Activity Logs

Navigation items should remain consistent across every page.

---

# 19.7 Dashboard Components

Dashboard components should use reusable cards.

Examples

Customer Dashboard

- Credit Card
- Loan Card
- Balance Card
- Timeline Card
- Notification Card

Administrator Dashboard

- Customer Statistics
- Pending Applications
- Active Loans
- Collections
- Activity Feed

---

# 19.8 Forms

Every form should follow consistent standards.

Requirements

✓ Labels

✓ Validation Messages

✓ Required Indicators

✓ Mobile Friendly

✓ Bootstrap Components

Forms should never rely solely on placeholder text.

---

# 19.9 Buttons

Button hierarchy should remain consistent.

Examples

Primary

- Submit
- Save
- Continue

Secondary

- Cancel
- Back

Danger

- Delete
- Reject
- Disable

Button colors belong to the Product Layer.

Button hierarchy belongs to the Framework.

---

# 19.10 Tables

Tables should support:

- Pagination
- Search
- Sorting
- Responsive Layout

Tables should remain readable on mobile devices whenever practical.

---

# 19.11 Cards

Cards are the primary presentation component.

Used for:

- Dashboard Widgets
- Statistics
- Credit Summary
- Loan Summary
- Customer Information

Cards should remain visually consistent.

---

# 19.12 Notifications

Notifications should distinguish:

Success

Information

Warning

Error

Notification styling belongs to the Framework.

---

# 19.13 Loading States

Every asynchronous action should provide user feedback.

Examples

- Loading Spinner
- Progress Indicator
- Disabled Button

Users should never wonder whether an operation is processing.

---

# 19.14 Empty States

Every module should gracefully handle missing data.

Examples

"No Active Loans"

"No Notifications"

"No Timeline Events"

Avoid blank pages.

---

# 19.15 Validation Messages

Validation should be:

- Clear
- Short
- Actionable

Avoid technical error messages.

Incorrect

"SQL Exception"

Correct

"Invalid mobile number."

---

# 19.16 UI Ownership

Framework owns:

- Components
- Layout
- Navigation
- Responsive Rules

AuraPay owns:

- Branding
- Theme
- Marketing
- Landing Page

---

# 19.17 Acceptance Criteria

The UI Design System is complete when:

✓ Responsive layout is implemented.

✓ Navigation remains consistent.

✓ Dashboard components are reusable.

✓ Forms follow consistent standards.

✓ Cards remain standardized.

✓ Validation is user-friendly.

✓ Product branding remains isolated.

---

# 20. PRODUCT LAYER

## 20.1 Purpose

The Product Layer separates reusable framework functionality from product-specific identity.

This is the most important architectural separation within the Lending Platform Framework.

Without this separation, every new lending application would require rewriting the framework.

---

# 20.2 Framework vs Product

Framework

Owns:

- Authentication
- Customers
- Credit Engine
- Loan Engine
- Timeline
- Activity Logs
- Configuration
- Security

Product

Owns:

- Brand
- Logo
- Colors
- Landing Page
- Marketing
- Product Name
- Schematics

---

# 20.3 Current Product

Current implementation:

```text
AuraPay
```

AuraPay represents the first implementation of the Lending Platform Framework.

---

# 20.4 AuraPay Identity

AuraPay defines:

- Logo
- Brand Name
- Marketing Copy
- Landing Page
- Dashboard Branding
- Product Graphics

These assets should remain completely replaceable.

---

# 20.5 Product Replacement

Future products should require replacement of only:

```text
Brand Name

Logo

Landing Page

Marketing

Color Palette

Assets

Schematics
```

The Framework remains unchanged.

---

# 20.6 Forbidden Practices

Never place:

- AuraPay colors
- AuraPay text
- AuraPay logo
- AuraPay marketing

inside reusable framework modules.

Only the Product Layer should contain branding.

---

# 20.7 Future Products

Examples

```text
AuraPay

↓

FastLoan

↓

CashFlow

↓

QuickPeso

↓

Future Product
```

All should reuse the exact same Framework.

---

# 20.8 Acceptance Criteria

The Product Layer is complete when:

✓ Framework remains reusable.

✓ Product branding is isolated.

✓ Landing Page is replaceable.

✓ Future products require no framework redesign.

---
END OF SECTION 13

# 21. AURAPAY PRODUCT LAYER

## 21.1 Purpose

AuraPay is the first product built using the Lending Platform Framework.

AuraPay is **NOT** the framework.

AuraPay is simply a product implementation.

The framework provides:

- Authentication
- Customer Management
- Credit Evaluation
- Loan Processing
- Timeline
- Activity Logging
- Security
- Configuration

AuraPay provides:

- Branding
- Marketing
- Visual Identity
- Landing Page
- Product Experience

---

# 21.2 Product Identity

Product Name

```text
AuraPay
```

Category

```text
Online Lending Platform
```

Platform

```text
Philippines
```

Currency

```text
Philippine Peso (PHP)
```

The platform should NEVER display USD unless explicitly configured for another product.

---

# 21.3 Brand Philosophy

AuraPay should present itself as:

- Professional
- Modern
- Fast
- Secure
- Mobile First
- Trustworthy

The user experience should focus on simplicity and speed rather than visual complexity.

---

# 21.4 Branding Assets

AuraPay branding consists of:

- Logo
- Product Name
- Hero Graphics
- Icons
- Marketing Images
- Color Palette
- Typography

These assets belong exclusively to AuraPay.

---

# 21.5 Product Theme

AuraPay should maintain one consistent visual identity across:

Landing Page

↓

Authentication

↓

Dashboard

↓

Loan Pages

↓

Profile

↓

Transactions

↓

Administrator

Brand consistency should never compromise usability.

---

# 21.6 Customer Experience Goals

The intended customer journey should feel:

Registration

↓

Fast

↓

Simple

↓

Guided

↓

Loan Application

↓

Transparent

↓

Repayment

↓

Easy

The interface should reduce customer confusion at every step.

---

# 21.7 Dashboard Branding

Customer Dashboard should include:

- AuraPay Logo
- Customer Greeting
- Credit Summary
- Available Credit
- Active Loan
- Timeline
- Notifications

Administrator Dashboard should include:

- AuraPay Branding
- Platform Statistics
- Pending Applications
- Active Loans
- Financial Overview
- Recent Activity

Branding should remain subtle and professional.

---

# 21.8 Landing Page Responsibilities

The Landing Page should:

- Introduce AuraPay.
- Explain the service.
- Build trust.
- Encourage registration.
- Present product benefits.
- Explain the loan process.
- Provide customer support access.

The Landing Page should NOT contain administrator access credentials.

---

# 21.9 Administrator Access

Administrator Login should NEVER appear as:

- A visible button on the public landing page.
- A public navigation item.
- Marketing content.

Administrator authentication should remain isolated from customer-facing content.

This prevents accidental exposure and reduces attack surface.

---

# 21.10 Currency Policy

AuraPay uses:

```text
Philippine Peso (₱ / PHP)
```

Every monetary value should display in Philippine Peso.

Examples:

- Credit Limit
- Loan Amount
- Remaining Balance
- Processing Fee
- Interest
- Payments

The framework should support future currency replacement through the Product Layer if another lending product targets a different country.

---

# 21.11 Product Isolation

AuraPay-specific files should remain isolated from framework modules.

Examples:

```text
assets/images/aurapay/

assets/css/aurapay/

assets/icons/aurapay/
```

Future products should replace only product assets without modifying reusable framework code.

---

# 21.12 Acceptance Criteria

AuraPay Product Layer is complete when:

✓ Branding is isolated.

✓ Philippine Peso is used consistently.

✓ Landing Page remains customer-focused.

✓ Administrator login is hidden from public navigation.

✓ Future products can replace AuraPay branding without modifying framework logic.

---

# 22. AURAPAY LANDING PAGE SCHEMATICS

## 22.1 Purpose

This section contains the complete structural blueprint for the AuraPay Landing Page.

Unlike the rest of the framework, this section is intentionally product-specific.

Future lending products should primarily replace this section while leaving the framework unchanged.

---

## 22.2 Landing Page Flow

```text
Hero Section

↓

Trust Indicators

↓

Why Choose AuraPay

↓

How It Works

↓

Loan Features

↓

Eligibility Requirements

↓

Frequently Asked Questions

↓

Customer Support

↓

Call To Action

↓

Footer
```

The landing page should tell a complete story from introduction to registration.

---

## 22.3 Hero Section

Primary responsibilities:

- Introduce AuraPay.
- Display primary call-to-action.
- Present brand identity.
- Build immediate trust.

Contents:

- Logo
- Headline
- Supporting Text
- Apply Now Button
- Sign In Button
- Hero Illustration

---

## 22.4 Trust Section

Purpose:

Increase customer confidence.

Examples:

- Secure Platform
- Fast Approval
- Transparent Fees
- Philippine-Based Service

---

## 22.5 How It Works

Explain the complete borrowing journey.

```text
Register

↓

Verify OTP

↓

Complete Profile

↓

Credit Evaluation

↓

Apply for Loan

↓

Approval

↓

Receive Funds

↓

Repay Loan
```

This section should remain simple and visual.

---

## 22.6 Features Section

Examples:

- Fast Registration
- Secure Authentication
- Transparent Loan Process
- Real-Time Timeline
- Mobile Friendly
- Secure Customer Dashboard

---

## 22.7 Eligibility Section

Display general eligibility information.

Examples:

- Philippine Resident
- Valid Government ID
- Active Mobile Number
- Income Requirement (if applicable)

Business rules remain inside the framework.

Landing page simply communicates them.

---

## 22.8 FAQ Section

Purpose:

Reduce common customer questions.

Examples:

- How long is approval?
- How do repayments work?
- What documents are required?
- How is my credit evaluated?

---

## 22.9 Customer Support

Display:

- Contact Information
- Business Hours
- Email
- Support Channels

No administrator information should appear here.

---

## 22.10 Footer

Contains:

- Copyright
- Privacy Policy
- Terms & Conditions
- Contact Information
- Social Links (optional)

---

## 22.11 Product Replacement Rule

When creating another lending product:

Replace:

- Branding
- Marketing Copy
- Graphics
- Theme
- Landing Page Schematics

Do NOT replace:

- Authentication
- Credit Engine
- Loan Engine
- Customer Module
- Administrator Module
- Timeline
- Activity Logging
- Configuration
- Security

This separation is the core purpose of the Lending Platform Framework.

---
END OF SECTION 14

# 23. AI IMPLEMENTATION DIRECTIVE

## 23.1 Purpose

This section serves as the master instruction set for any AI software engineer tasked with building the Lending Platform Framework.

This specification should be treated as the single authoritative engineering blueprint.

The AI must read this document completely before writing any source code.

---

# 23.2 Primary Objective

Your objective is NOT to redesign the platform.

Your objective is to faithfully implement the system exactly as documented.

This document defines:

- Architecture
- Workflows
- Filesystem
- Business Rules
- Framework Philosophy
- Product Layer
- Landing Page

Do not replace any of them with your own ideas.

---

# 23.3 Existing Repository

The existing repository should be considered an unfinished prototype.

It currently contains remnants of multiple development attempts including:

- PHP
- React
- TypeScript
- Vite
- Duplicate Pages
- Duplicate Dashboards
- Mixed Folder Structures

These remnants should NOT dictate implementation.

Instead,

use this document as the architectural reference.

---

# 23.4 Framework Priority

Whenever there is a conflict between:

Existing Source Code

and

This Specification

Always follow

THIS SPECIFICATION.

Source code may be rewritten.

Architecture should not.

---

# 23.5 PHP Requirement

This project is a

PHP Full Stack Web Application.

It is NOT a React application.

It is NOT a Vite application.

It is NOT a TypeScript SPA.

The target stack is:

```text
PHP

MySQL

HTML

Bootstrap

JavaScript
```

React remnants should be removed or replaced whenever necessary.

---

# 23.6 Filesystem Compliance

The documented filesystem is mandatory.

Do NOT:

- Create unnecessary folders.
- Invent a different architecture.
- Merge unrelated modules.
- Ignore documented directories.

Every generated file must belong to the documented filesystem.

---

# 23.7 No Partial Implementation

Every generated file must be fully implemented.

Never generate:

```text
TODO

Coming Soon

Placeholder

Stub

pass

throw new Error()

return null

return [];
```

Every file should be production-ready.

---

# 23.8 No Skeleton Files

Do not generate empty classes.

Do not generate empty controllers.

Do not generate empty PHP pages.

Every generated file should contain complete implementation according to its documented responsibility.

---

# 23.9 Workflow Compliance

Every workflow documented inside this specification is mandatory.

Examples:

Registration

↓

OTP

↓

Profile Completion

↓

Credit Evaluation

↓

Loan Application

↓

Administrator Review

↓

Loan Release

↓

Repayment

↓

Completion

Do not simplify or reorder workflows.

---

# 23.10 Business Rule Compliance

Never bypass:

- Credit Evaluation
- Authentication
- Permission Validation
- Configuration System
- Timeline Generation
- Activity Logging

Every module should follow documented Business Rules.

---

# 23.11 Product Layer Compliance

AuraPay is only the current implementation.

Framework modules must remain reusable.

Do not hardcode AuraPay branding into:

- Authentication
- Credit Engine
- Loan Engine
- Timeline
- Configuration
- Security

AuraPay branding belongs exclusively to the Product Layer.

---

# 23.12 Landing Page Compliance

The Landing Page should follow the documented schematics exactly.

Do not redesign the customer journey.

Administrator login should remain isolated.

Never expose administrator credentials or administrator entry points on the public landing page.

---

# 23.13 Deployment Notice

Do NOT refuse implementation because you cannot execute PHP.

Do NOT refuse implementation because a PHP runtime is unavailable.

Do NOT refuse implementation because MySQL is unavailable.

Deployment is handled separately.

Your responsibility is:

Generate complete production-quality source code.

Runtime execution is not your responsibility.

---

# 23.14 Coding Standards

Generated code should be:

✓ Clean

✓ Modular

✓ Maintainable

✓ Production Ready

✓ Well Structured

✓ Secure

✓ Consistent

Avoid unnecessary abstraction.

Avoid duplicated code.

Prefer helper reuse over repeated logic.

---

# 23.15 Final Deliverable

The completed project should satisfy all sections of this specification.

The resulting application should require minimal manual modification before deployment.

The objective is to produce a fully functioning PHP lending platform based on this document.

---

# 24. MASTER BUILD CHECKLIST

The project is considered complete only when all of the following have been implemented.

## Framework

- [ ] Architecture implemented
- [ ] Filesystem followed exactly
- [ ] Database completed
- [ ] Configuration centralized
- [ ] Helper Library completed

---

## Authentication

- [ ] Registration
- [ ] OTP Verification
- [ ] Login
- [ ] Logout
- [ ] Password Recovery
- [ ] Session Validation

---

## Customer Module

- [ ] Dashboard
- [ ] Profile
- [ ] Document Upload
- [ ] Timeline
- [ ] Notifications

---

## Credit Engine

- [ ] Credit Evaluation
- [ ] Credit Score
- [ ] Credit Limit
- [ ] Reevaluation

---

## Loan Engine

- [ ] Loan Application
- [ ] Approval Workflow
- [ ] Loan Release
- [ ] Payments
- [ ] Completion

---

## Administrator

- [ ] Dashboard
- [ ] Customers
- [ ] Loans
- [ ] Credit
- [ ] Reports
- [ ] Configuration
- [ ] Activity Logs

---

## Security

- [ ] Password Hashing
- [ ] Permission Validation
- [ ] SQL Injection Protection
- [ ] Upload Validation
- [ ] Activity Logging

---

## Product Layer

- [ ] AuraPay Branding
- [ ] Landing Page
- [ ] Schematics Implemented
- [ ] Philippine Peso Used Throughout

---

## Documentation

- [ ] Framework matches specification
- [ ] No duplicate implementations
- [ ] No placeholder code
- [ ] No incomplete files
- [ ] Production-ready project

---

# 25. CANONICAL DATABASE SCHEMA

## 25.1 Purpose

This section defines the canonical database schema for the Lending Platform Framework.

This schema is the authoritative database structure.

Every implementation must follow this schema unless explicitly extended.

Table names, ownership, and relationships should remain consistent across every product built using this framework.

---

# DATABASE PRINCIPLES

The database should follow the following principles:

✓ Normalized

✓ Consistent Naming

✓ Primary Keys

✓ Foreign Keys

✓ Immutable Historical Records

✓ No Duplicate Data

✓ Audit Friendly

✓ Framework Reusable

---

# DATABASE OWNERSHIP

The Framework owns:

- Users
- Authentication
- Credit
- Loans
- Timeline
- Activity Logs
- Notifications
- Transactions
- Configuration

AuraPay owns:

NONE

AuraPay should not introduce framework tables.

---

# TABLE 01

## users

Purpose

Stores customer accounts.

Columns

```text
id (PK)

mobile_number

password_hash

otp_verified

account_status

customer_status

created_at

updated_at
```

Relationships

```text
users

↓

customer_profiles

↓

credit_limits

↓

loan_applications

↓

loans

↓

transactions

↓

timeline

↓

notifications
```

---

# TABLE 02

## customer_profiles

Purpose

Stores complete customer profile information.

Columns

```text
id (PK)

user_id (FK)

first_name

middle_name

last_name

birth_date

gender

civil_status

nationality

email

present_address

city

province

postal_code

employment_status

occupation

employer

monthly_income

emergency_contact_name

emergency_contact_number

government_id_type

government_id_number

profile_completed

created_at

updated_at
```

---

# TABLE 03

## customer_documents

Purpose

Stores uploaded customer verification documents.

Columns

```text
id (PK)

user_id (FK)

document_type

file_name

file_path

mime_type

file_size

verification_status

uploaded_at

verified_at
```

Document Examples

```text
Government ID

Selfie

Proof of Income

Proof of Billing
```

---

# TABLE 04

## otp_codes

Purpose

Stores OTP verification history.

Columns

```text
id (PK)

user_id (FK)

mobile_number

otp_code

expires_at

verified_at

status

created_at
```

Historical OTPs should remain stored.

Never overwrite previous OTP records.

---

# TABLE 05

## credit_evaluations

Purpose

Stores every customer credit evaluation.

Columns

```text
id (PK)

user_id (FK)

credit_score

risk_category

evaluation_result

remarks

evaluated_by

evaluated_at
```

Each reevaluation creates a NEW record.

Never overwrite previous evaluations.

---

# TABLE 06

## credit_limits

Purpose

Stores current customer credit information.

Columns

```text
id (PK)

user_id (FK)

approved_limit

available_limit

used_limit

status

last_evaluated_at

updated_at
```

Only one active credit limit exists per customer.

---

# TABLE 07

## loan_applications

Purpose

Stores submitted loan applications.

Columns

```text
id (PK)

user_id (FK)

requested_amount

loan_term

loan_purpose

application_status

submitted_at

reviewed_at

reviewed_by
```

Applications remain permanent.

Never delete application history.

---

# TABLE 08

## loans

Purpose

Stores approved loans.

Columns

```text
id (PK)

application_id (FK)

user_id (FK)

principal_amount

interest_amount

processing_fee

total_amount

remaining_balance

loan_status

release_date

due_date

completed_at
```

Loans originate only from approved applications.

---

# TABLE 09

## loan_payments

Purpose

Stores repayment history.

Columns

```text
id (PK)

loan_id (FK)

user_id (FK)

payment_amount

payment_method

payment_reference

remaining_balance

payment_date
```

Every payment creates a new record.

Payments are immutable.

---

# TABLE 10

## transactions

Purpose

Stores financial transaction history.

Columns

```text
id (PK)

user_id (FK)

loan_id (FK)

transaction_type

amount

reference_number

description

transaction_date
```

Examples

```text
Loan Release

Loan Payment

Adjustment

Refund
```

---

# TABLE 11

## timeline

Purpose

Stores customer timeline events.

Columns

```text
id (PK)

user_id (FK)

event_type

title

description

created_at
```

Timeline is customer-facing.

Never modify previous entries.

---

# TABLE 12

## notifications

Purpose

Stores customer notifications.

Columns

```text
id (PK)

user_id (FK)

title

message

notification_type

is_read

created_at
```

---

# TABLE 13

## administrators

Purpose

Stores administrator accounts.

Columns

```text
id (PK)

username

password_hash

role_id (FK)

status

created_at

updated_at
```

Administrator passwords should always be hashed.

---

# TABLE 14

## administrator_roles

Purpose

Stores administrator roles.

Columns

```text
id (PK)

role_name

description
```

Examples

```text
Super Administrator

Loan Officer

Finance Officer

Customer Support

Compliance Officer
```

---

# TABLE 15

## permissions

Purpose

Stores permission definitions.

Columns

```text
id (PK)

permission_name

description
```

---

# TABLE 16

## role_permissions

Purpose

Maps permissions to administrator roles.

Columns

```text
role_id (FK)

permission_id (FK)
```

Many-to-many relationship.

---

# TABLE 17

## activity_logs

Purpose

Stores administrator and system audit logs.

Columns

```text
id (PK)

administrator_id (FK)

action

target

description

ip_address

created_at
```

Activity Logs are immutable.

---

# TABLE 18

## configuration

Purpose

Stores framework configuration.

Columns

```text
id (PK)

config_key

config_value

description

updated_by

updated_at
```

Examples

```text
interest_rate

processing_fee

minimum_loan

maximum_loan

maintenance_mode

otp_expiration
```

---

# TABLE RELATIONSHIP SUMMARY

```text
users

├── customer_profiles

├── customer_documents

├── otp_codes

├── credit_evaluations

├── credit_limits

├── loan_applications

│      └── loans

│              └── loan_payments

├── transactions

├── timeline

└── notifications

administrators

├── administrator_roles

├── permissions

├── role_permissions

└── activity_logs

configuration
```

---

# DATABASE RULES

The database must enforce:

✓ Primary Keys

✓ Foreign Keys

✓ Referential Integrity

✓ Immutable Historical Records

✓ Soft Deletion where applicable

✓ Timestamp Tracking

✓ Framework Reusability

---

# ACCEPTANCE CRITERIA

The Canonical Database Schema is complete when:

✓ Every framework module has dedicated tables.

✓ Relationships are clearly defined.

✓ Historical data is preserved.

✓ No duplicated entities exist.

✓ Future lending products can reuse the exact same schema.

---
END OF SECTION 15

# 26. CANONICAL FILESYSTEM TREE

## 26.1 Purpose

This section defines the official filesystem structure of the Lending Platform Framework.

Every implementation must follow this directory structure.

The filesystem separates responsibilities clearly to maximize maintainability and future product reuse.

---

# ROOT DIRECTORY

```text
/

index.php

.htaccess

README.md

MASTER_BUILD_SPECIFICATION.md

composer.json (optional)

.env (optional)
```

---

# CONFIGURATION

```text
config/

app.php

database.php

session.php

constants.php

routes.php
```

Purpose

Stores all framework configuration.

Business values should be loaded through the Configuration Helper.

---

# DATABASE

```text
database/

schema.sql

seed.sql

migrations/
```

Purpose

Contains database initialization and future migrations.

---

# HELPERS

```text
helpers/

auth_helper.php

customer_helper.php

profile_helper.php

credit_helper.php

risk_helper.php

loan_helper.php

payment_helper.php

transaction_helper.php

timeline_helper.php

activity_helper.php

notification_helper.php

upload_helper.php

validation_helper.php

configuration_helper.php

permission_helper.php

report_helper.php
```

Purpose

Contains all reusable business logic.

Presentation pages must never duplicate helper logic.

---

# CUSTOMER PAGES

```text
pages/

home.php

login.php

register.php

verify_otp.php

forgot_password.php

reset_password.php

dashboard.php

profile.php

documents.php

credit.php

loan.php

loan_history.php

loan_details.php

payments.php

transactions.php

timeline.php

notifications.php

settings.php

logout.php
```

Purpose

Contains all customer-facing pages.

---

# ADMINISTRATOR

```text
admin/

login.php

dashboard.php

customers.php

customer_details.php

applications.php

application_details.php

loans.php

loan_details.php

credit.php

transactions.php

reports.php

activity_logs.php

configuration.php

maintenance.php

logout.php
```

Purpose

Contains administrator-only modules.

Administrator pages must always verify:

- Authentication
- Permission
- Role

before rendering.

---

# API (OPTIONAL)

```text
api/

auth/

customer/

loan/

credit/

notification/

admin/
```

Purpose

Reserved for future API expansion.

Not required for the first implementation.

---

# ASSETS

```text
assets/

css/

js/

images/

icons/

fonts/
```

Purpose

Stores reusable frontend assets.

---

# PRODUCT ASSETS

```text
assets/

products/

aurapay/

logo/

images/

icons/

theme/
```

Purpose

Stores AuraPay branding.

Future products should replace only this folder.

---

# UPLOADS

```text
uploads/

government_ids/

selfies/

proof_of_income/

proof_of_billing/

temporary/
```

Purpose

Stores uploaded customer documents.

Uploads should never be executable.

---

# INCLUDES

```text
includes/

header.php

footer.php

navbar.php

sidebar.php

customer_menu.php

admin_menu.php
```

Purpose

Reusable layout components.

---

# AUTHENTICATION

```text
auth/

login.php

logout.php

register.php

verify.php

forgot.php

reset.php
```

Purpose

Authentication routing (optional organization depending on implementation).

---

# LOGS

```text
logs/

application/

security/

errors/
```

Purpose

Stores application logs.

Sensitive data should never be written to logs.

---

# TEMP

```text
storage/

cache/

sessions/

temp/
```

Purpose

Temporary runtime storage.

---

# FILE OWNERSHIP

## Framework

Owns

```text
config

database

helpers

pages

admin

uploads

logs

storage
```

---

## Product

Owns

```text
assets/products/aurapay/
```

Only branding belongs here.

---

# FILE NAMING RULES

Use:

```text
snake_case.php
```

Examples

```text
loan_helper.php

activity_logs.php

customer_profile.php
```

Avoid:

CamelCase

PascalCase

Mixed Naming

---

# DIRECTORY RULES

One responsibility per directory.

Examples

```text
helpers/

↓

Business Logic
```

```text
pages/

↓

Customer UI
```

```text
admin/

↓

Administrator UI
```

Never mix administrator files with customer pages.

---

# FORBIDDEN STRUCTURES

Do NOT create:

```text
src/

client/

server/

frontend/

backend/

components/

hooks/

services/

redux/

react/

vite/
```

These belong to different architectures.

This framework follows a PHP Full Stack architecture.

---

# CANONICAL FILESYSTEM SUMMARY

```text
/

config/

database/

helpers/

pages/

admin/

assets/

uploads/

includes/

logs/

storage/
```

This is the authoritative project structure.

---

# ACCEPTANCE CRITERIA

The Canonical Filesystem is complete when:

✓ Every module has a dedicated directory.

✓ Customer and administrator pages are separated.

✓ Business logic remains inside helpers.

✓ Product assets remain isolated.

✓ Future products reuse the exact same structure.

---
END OF SECTION 16

# 27. REQUIRED PAGES & RESPONSIBILITIES

## 27.1 Purpose

This section defines every required page within the Lending Platform Framework.

Every page has a single responsibility.

Do NOT merge unrelated responsibilities into one page.

Do NOT split a documented page into multiple pages unless explicitly required by future framework updates.

---

# CUSTOMER PAGES

---

## home.php

Purpose

Public Landing Page

Responsibilities

- Display AuraPay branding
- Present product information
- Explain lending process
- Display trust indicators
- Redirect users to Login/Register
- Display FAQ
- Display Support Information

Must NOT:

- Display administrator login
- Display administrator credentials
- Contain dashboard functionality

---

## login.php

Purpose

Customer Authentication

Responsibilities

- Authenticate customer
- Validate credentials
- Start customer session
- Redirect to dashboard
- Generate Activity Log

Must NOT:

- Register customers
- Reset passwords

---

## register.php

Purpose

Customer Registration

Responsibilities

- Register customer
- Validate mobile number
- Generate OTP
- Store account
- Redirect to OTP verification

Must NOT:

- Complete customer profile
- Evaluate credit

---

## verify_otp.php

Purpose

OTP Verification

Responsibilities

- Verify OTP
- Activate account
- Redirect to Profile Completion

---

## forgot_password.php

Purpose

Password Recovery

Responsibilities

- Verify mobile number
- Generate reset OTP
- Begin password recovery

---

## reset_password.php

Purpose

Password Reset

Responsibilities

- Validate OTP
- Update password
- Force password hashing

---

## dashboard.php

Purpose

Customer Dashboard

Responsibilities

Display:

- Credit Summary
- Available Credit
- Active Loan
- Recent Timeline
- Notifications
- Quick Actions

Dashboard should NOT contain business logic.

---

## profile.php

Purpose

Customer Profile

Responsibilities

- View profile
- Update profile
- Validate profile
- Complete profile

---

## documents.php

Purpose

Customer Document Upload

Responsibilities

- Upload Government ID
- Upload Selfie
- Upload Income Documents
- Display upload status

---

## credit.php

Purpose

Customer Credit Information

Responsibilities

Display:

- Credit Status
- Credit Limit
- Available Credit
- Evaluation Date

Customers cannot modify credit.

---

## loan.php

Purpose

Loan Application

Responsibilities

- Display application form
- Validate eligibility
- Submit application
- Generate Timeline
- Generate Activity Log

---

## loan_history.php

Purpose

Loan History

Responsibilities

Display:

- Pending Applications
- Active Loans
- Completed Loans
- Rejected Applications

---

## loan_details.php

Purpose

Loan Details

Responsibilities

Display:

- Loan Summary
- Balance
- Interest
- Payment History

---

## payments.php

Purpose

Loan Repayments

Responsibilities

- Display repayment information
- Record payment
- Update balance
- Generate Timeline

---

## transactions.php

Purpose

Transaction History

Responsibilities

Display all financial transactions.

Read Only.

---

## timeline.php

Purpose

Customer Timeline

Responsibilities

Display chronological customer events.

Read Only.

---

## notifications.php

Purpose

Customer Notifications

Responsibilities

Display notifications.

Mark notifications as read.

---

## settings.php

Purpose

Customer Settings

Responsibilities

- Update password
- Update account settings

---

## logout.php

Purpose

Destroy customer session.

Redirect to Landing Page.

---

# ADMINISTRATOR PAGES

---

## admin/login.php

Purpose

Administrator Authentication

Responsibilities

- Authenticate administrator
- Validate role
- Start administrator session

---

## admin/dashboard.php

Purpose

Administrator Dashboard

Responsibilities

Display:

- Customer Statistics
- Pending Applications
- Active Loans
- Financial Summary
- Recent Activity

---

## admin/customers.php

Purpose

Customer Management

Responsibilities

- Search customers
- View customers
- Filter customers

---

## admin/customer_details.php

Purpose

Customer Profile Review

Responsibilities

Display:

- Customer Profile
- Documents
- Timeline
- Loan History
- Credit Information

---

## admin/applications.php

Purpose

Loan Application Review

Responsibilities

- Review applications
- Filter applications
- Open application details

---

## admin/application_details.php

Purpose

Application Review

Responsibilities

- View application
- Approve
- Reject
- Add remarks

Generate Activity Logs.

---

## admin/loans.php

Purpose

Loan Management

Responsibilities

Display:

- Active Loans
- Completed Loans
- Outstanding Balances

---

## admin/loan_details.php

Purpose

Loan Review

Responsibilities

Display complete loan information.

---

## admin/credit.php

Purpose

Credit Management

Responsibilities

- View evaluations
- Trigger reevaluation
- Override limits (if permitted)

---

## admin/transactions.php

Purpose

Transaction Monitoring

Responsibilities

Display financial transactions.

Read Only.

---

## admin/reports.php

Purpose

Reporting

Responsibilities

Generate:

- Customer Reports
- Loan Reports
- Financial Reports
- Activity Reports

---

## admin/activity_logs.php

Purpose

Audit Viewer

Responsibilities

Display administrator Activity Logs.

Read Only.

---

## admin/configuration.php

Purpose

Platform Configuration

Responsibilities

Modify:

- Interest
- Fees
- Limits
- Maintenance
- Platform Settings

Generate Activity Logs.

---

## admin/maintenance.php

Purpose

Maintenance Mode

Responsibilities

Enable

Disable

Maintenance Mode.

---

## admin/logout.php

Purpose

Destroy administrator session.

---

# PAGE RULES

Every protected page must verify:

Authentication

↓

Authorization

↓

Permission

before rendering.

---

# PAGE OWNERSHIP

Customer Pages

↓

Customer Module

Administrator Pages

↓

Administrator Module

Landing Page

↓

Product Layer

Helpers

↓

Business Logic

---

# ACCEPTANCE CRITERIA

Required Pages are complete when:

✓ Every responsibility belongs to exactly one page.

✓ Customer pages remain customer-only.

✓ Administrator pages remain administrator-only.

✓ Landing Page remains product-specific.

✓ Business logic remains outside presentation pages.

---
END OF SECTION 17

# 28. REQUIRED FORM FIELDS

## 28.1 Purpose

This section defines the canonical form fields used throughout the Lending Platform Framework.

Every implementation should follow these forms unless future framework updates explicitly extend them.

The purpose of this section is to eliminate ambiguity during implementation.

---

# GENERAL FORM RULES

Every form should:

✓ Validate all required fields

✓ Perform server-side validation

✓ Display clear validation messages

✓ Use Bootstrap form components

✓ Prevent duplicate submissions

✓ Sanitize user input

---

# CUSTOMER REGISTRATION

## register.php

Purpose

Create a new customer account.

Fields

```text
Mobile Number *

Password *

Confirm Password *
```

Validation

- Mobile Number must be unique.
- Password must satisfy framework password policy.
- Confirm Password must match Password.

After successful registration:

↓

Generate OTP

↓

Redirect to verify_otp.php

---

# OTP VERIFICATION

## verify_otp.php

Fields

```text
OTP Code *
```

Validation

- OTP exists.
- OTP not expired.
- OTP belongs to current customer.

After verification:

↓

Activate Account

↓

Redirect Profile Completion

---

# LOGIN

## login.php

Fields

```text
Mobile Number *

Password *
```

Validation

- Account exists.
- Password correct.
- Account active.
- OTP verified.

---

# FORGOT PASSWORD

## forgot_password.php

Fields

```text
Mobile Number *
```

Validation

- Customer exists.

After validation:

↓

Generate Reset OTP

---

# RESET PASSWORD

## reset_password.php

Fields

```text
OTP *

New Password *

Confirm Password *
```

Validation

- OTP valid.
- Passwords match.

---

# CUSTOMER PROFILE

## profile.php

Fields

### Personal Information

```text
First Name *

Middle Name

Last Name *

Birth Date *

Gender *

Civil Status *

Nationality *
```

---

### Contact Information

```text
Email

Present Address *

City *

Province *

Postal Code *
```

---

### Employment

```text
Employment Status *

Occupation *

Employer *

Monthly Income *
```

---

### Emergency Contact

```text
Emergency Contact Name *

Emergency Contact Number *
```

---

### Government Identification

```text
Government ID Type *

Government ID Number *
```

---

Validation

Profile is considered complete only when all required fields are valid.

---

# DOCUMENT UPLOAD

## documents.php

Government ID

Fields

```text
Government ID Type *

Government ID Image *
```

---

Selfie Verification

Fields

```text
Selfie Image *
```

---

Income Verification

Fields

```text
Proof of Income
```

Optional unless required by future policy.

---

Billing Verification

Fields

```text
Proof of Billing
```

Optional unless required.

---

Validation

Allowed:

- JPG
- JPEG
- PNG
- PDF (where applicable)

Maximum upload size should originate from Configuration.

---

# CREDIT EVALUATION

Customer does not manually complete this form.

Administrator/System only.

Fields

```text
Credit Score

Risk Category

Credit Limit

Evaluation Remarks
```

These are generated by the Credit Engine.

---

# LOAN APPLICATION

## loan.php

Fields

```text
Requested Amount *

Loan Purpose *

Loan Term *
```

Validation

- Amount within available credit.
- Amount within configured limits.
- Customer eligible.

---

# LOAN APPROVAL

## admin/application_details.php

Fields

```text
Decision *

Administrative Remarks
```

Decision

```text
Approve

Reject
```

Approval should automatically create:

- Loan
- Timeline Event
- Activity Log

---

# PAYMENT

## payments.php

Fields

```text
Payment Amount *

Payment Method *

Reference Number
```

Payment Methods

Examples

```text
GCash

Maya

Bank Transfer

Cash
```

Payment recording automatically updates:

- Remaining Balance
- Transactions
- Timeline
- Activity Logs

---

# CUSTOMER SETTINGS

## settings.php

Password Change

Fields

```text
Current Password *

New Password *

Confirm Password *
```

Validation

- Current password correct.
- New passwords match.

---

# ADMINISTRATOR LOGIN

## admin/login.php

Fields

```text
Username *

Password *
```

Validation

- Administrator exists.
- Password valid.
- Role active.

---

# CONFIGURATION

## admin/configuration.php

Examples

```text
Interest Rate *

Processing Fee *

Minimum Loan *

Maximum Loan *

OTP Expiration *

Maintenance Mode *
```

Every change generates an Activity Log.

---

# SEARCH FORMS

Customer Search

```text
Customer Name

Mobile Number
```

Loan Search

```text
Loan ID

Customer

Status
```

Transaction Search

```text
Reference Number

Customer

Date Range
```

Activity Search

```text
Administrator

Action

Date Range
```

---

# REQUIRED FIELD LEGEND

```text
* = Mandatory

Blank = Optional
```

---

# FORM OWNERSHIP

Authentication Forms

↓

Authentication Module

Profile Forms

↓

Customer Module

Loan Forms

↓

Loan Engine

Credit Forms

↓

Credit Engine

Configuration Forms

↓

Configuration Module

Administrative Forms

↓

Administrator Module

---

# ACCEPTANCE CRITERIA

Required Form Fields are complete when:

✓ Every page has documented input fields.

✓ Required fields are clearly identified.

✓ Validation rules are documented.

✓ Form ownership is defined.

✓ Future implementations can recreate every form without guessing.

---

# END OF MASTER_BUILD_SPECIFICATION

This document now contains:

✓ AI Execution Prompt

✓ Architecture

✓ Technology Stack

✓ Canonical Filesystem

✓ Canonical Database Schema

✓ Authentication

✓ Customer Module

✓ Credit Engine

✓ Loan Engine

✓ Timeline

✓ Activity Logs

✓ Administrator Module

✓ Configuration

✓ Helper Library

✓ Security

✓ Business Rules

✓ UI Design System

✓ Product Layer

✓ AuraPay Product Layer

✓ AuraPay Landing Page Schematics

✓ Required Pages

✓ Required Forms

✓ Final Build Checklist

This specification is intended to serve as the single authoritative engineering blueprint for building the Lending Platform Framework and any future lending products based upon it.