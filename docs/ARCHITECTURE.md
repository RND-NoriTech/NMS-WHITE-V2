# NMS-WHITE Architecture

## 1. Purpose

NMS-WHITE is a LibreNMS-based Network Management System (NMS) with:

- Custom branding
- Custom dashboard
- API
- Multi-tenancy
- Docker deployment
- Native installation support

The core architectural principle is:

> **LibreNMS = monitoring engine**
> **NMS-WHITE = product/application layer**

NMS-WHITE should add product-specific functionality without unnecessarily modifying LibreNMS core.

---

## 2. Target Architecture

```text
                    NMS-WHITE
                        |
          +-------------+-------------+
          |                           |
      NMS-WHITE UI               NMS-WHITE API
          |                           |
          +-------------+-------------+
                        |
                  LibreNMS Core
                        |
       +----------------+----------------+
       |                |                |
      SNMP            Poller          Alerting
       |                |                |
       +----------------+----------------+
                        |
                 MariaDB / Redis
                        |
          +-------------+-------------+
          |             |             |
        Switch          AP          Router
```

LibreNMS provides the underlying monitoring capabilities.

NMS-WHITE provides the product/application layer around LibreNMS.

---

## 3. Repository Strategy

NMS-WHITE is maintained as a standalone repository.

The repository maintains an explicit Git relationship with LibreNMS through an `upstream` remote.

```text
origin
  |
  +-- RND-NoriTech/NMS-WHITE-V2

upstream
  |
  +-- librenms/librenms
```

The repository must preserve the ability to fetch and integrate upstream LibreNMS changes.

The current LibreNMS upstream branch is:

```text
master
```

The Git remotes are:

```bash
git remote -v
```

Expected configuration:

```text
origin   https://github.com/RND-NoriTech/NMS-WHITE-V2.git
upstream https://github.com/librenms/librenms.git
```

NMS-WHITE does not depend on the GitHub fork relationship with LibreNMS. The upstream relationship is maintained explicitly through Git.

---

## 4. Branch Strategy

The repository uses a development branch strategy that separates stable code from active development.

```text
master
  |
  +-- stable / upstream-aligned baseline
          |
          v
       develop
          |
          +-- feature/*
```

### Branches

#### `master`

`master` is the stable, upstream-aligned baseline.

The `master` branch is protected on GitHub.

Normal development should not be performed directly on `master`.

#### `develop`

`develop` is the main integration branch for NMS-WHITE development.

Feature branches are merged into `develop` after review and testing.

#### Feature branches

Individual development work should use feature branches.

Examples:

```text
feature/phase-0-foundation
feature/branding
feature/dashboard
feature/api
feature/multi-tenancy
feature/docker-production
```

The intended development flow is:

```text
feature/*
    |
    v
develop
    |
    v
master
```

---

## 5. Upstream Synchronization

LibreNMS remains the upstream monitoring project.

The intended synchronization model is:

```text
LibreNMS
upstream/master
       |
       v
     master
       |
       v
    develop
       |
       +-- feature/*
```

The first step when checking for upstream changes is:

```bash
git fetch upstream
```

The current upstream branch is:

```text
upstream/master
```

Upstream changes must be reviewed and tested before they are promoted into the stable NMS-WHITE branch.

### Synchronization principles

1. Keep the LibreNMS upstream remote available.
2. Fetch upstream changes deliberately.
3. Review upstream changes before integration.
4. Test after upstream integration.
5. Verify NMS-WHITE-specific functionality.
6. Minimize unnecessary changes to LibreNMS core.
7. Keep product-specific functionality separated from upstream functionality where practical.

The objective is to make future LibreNMS updates predictable and reduce merge conflicts.

---

## 6. Application Boundary

NMS-WHITE is the product/application layer around LibreNMS.

Preferred areas for NMS-WHITE-specific functionality include:

- Custom dashboard
- Custom navigation
- Custom API
- Branding
- Reports
- Tenant functionality
- Custom settings
- Network maps
- Product-specific modules
- Extension/plugin mechanisms

The architectural preference is:

```text
NMS-WHITE
    |
    +-- Product/Application Layer
            |
            +-- Dashboard
            +-- API
            +-- Branding
            +-- Reports
            +-- Tenant
            +-- Other NMS-WHITE modules
            |
            v
       LibreNMS Core
            |
            +-- SNMP
            +-- Polling
            +-- Alerting
            +-- Discovery
            +-- Graphs
            +-- Sensors
```

LibreNMS core should only be modified when there is no reasonable extension point.

Avoiding unnecessary core changes reduces upstream merge conflicts and makes future LibreNMS upgrades easier.

---

## 7. Database Strategy

LibreNMS remains responsible for its existing monitoring data model.

NMS-WHITE-specific application data should be kept separate where practical.

NMS-WHITE-specific tables should use the `nms_` naming convention.

Planned examples include:

```text
nms_tenants
nms_tenant_devices
```

The intended model is:

```text
LibreNMS data
    |
    +-- Devices
    +-- Interfaces
    +-- Polling data
    +-- Alerts
    +-- Sensors
    +-- Graphs
    +-- Monitoring configuration

NMS-WHITE data
    |
    +-- Product configuration
    +-- Tenants
    +-- Tenant/device relationships
    +-- Product-specific application data
```

NMS-WHITE should avoid unnecessary modifications to existing LibreNMS tables.

Multi-tenant database implementation belongs to the later Multi-Tenant phase.

---

## 8. Deployment Strategy

The project must use a single application codebase.

Docker and native installation must not become separate application implementations.

```text
              NMS-WHITE SOURCE
                     |
             +-------+-------+
             |               |
          Native           Docker
        installer       compose/images
             |               |
             +-------+-------+
                     |
              SAME APPLICATION
```

Docker is the primary development and initial deployment target.

Native installation is added after the Docker deployment is stable.

Both deployment methods must use the same NMS-WHITE application source.

The architecture must therefore avoid maintaining:

```text
NMS-WHITE Docker Application
+
NMS-WHITE Native Application
```

as two independent implementations.

---

## 9. Docker-First Development

Phase 1 begins with a clean LibreNMS environment before major NMS-WHITE customization.

The initial target is:

```text
Ubuntu
  |
Docker
  |
LibreNMS
  |
First device
  |
SNMP
  |
Polling
  |
Graphs
  |
Alerts
```

The Docker environment should provide the required LibreNMS services:

```text
Docker
 |
 +-- LibreNMS
 |
 +-- MariaDB
 |
 +-- Redis
 |
 +-- Poller
```

Phase 1 should verify:

- Docker installation
- LibreNMS deployment
- MariaDB configuration
- Redis configuration
- Poller configuration
- First network device
- SNMP connectivity
- Polling
- Alerts
- API
- Auto-discovery
- Graphs
- Sensors

The monitoring foundation should be stable before major NMS-WHITE customization begins.

---

## 10. Branding Boundary

NMS-WHITE branding should make LibreNMS look and feel like the NMS-WHITE product without unnecessarily modifying LibreNMS core.

Planned branding areas include:

- Logo
- Favicon
- Product name
- Browser title
- Login page
- Custom CSS
- Custom colors
- Dashboard branding
- Email branding

Branding must not globally replace every occurrence of LibreNMS.

The following must be preserved where required:

- License notices
- Copyright notices
- Third-party attribution
- Required GPLv3 information
- Technical references where required

Branding and licensing must therefore remain separate concerns.

---

## 11. GPLv3 Compliance

NMS-WHITE is based on LibreNMS and must preserve required LibreNMS licensing, copyright, and attribution information.

NMS-WHITE must not remove or obscure legally required licensing information for branding purposes.

GPLv3 compliance must be documented and reviewed before commercial distribution.

The final commercial licensing and distribution structure should receive appropriate legal review before release.

This is particularly important for the planned Community, Professional, and Enterprise product structure.

---

## 12. Security and Tenant Boundary

Multi-tenancy is a later development phase.

When implemented, the architecture must enforce strict tenant isolation.

The roadmap defines the critical requirement:

```text
Tenant A
   |
   X
   |
Tenant B data
```

Tenant A must never be able to access Tenant B data.

This isolation must apply across:

- Web UI
- API
- Application routes
- Device access
- Alerts
- Reports
- Tenant-specific data

Tenant isolation must be covered by automated authorization and isolation tests.

Security implementation belongs to the later Multi-Tenant and Security phases and is not part of the initial Phase 0 implementation.

---

## 13. CI/CD Strategy

CI/CD is part of the Phase 0 foundation.

The long-term update flow is:

```text
LibreNMS upstream
       |
       v
GitHub upstream
       |
       v
NMS-WHITE integration
       |
       v
Automated tests
       |
       v
Build
       |
       v
Release
       |
       +-- Docker
       |
       +-- Native
```

The CI/CD system should eventually verify:

- Build
- Unit tests
- Integration tests
- Docker tests
- Application tests
- Security checks

The full production CI/CD pipeline is implemented progressively rather than all at once during Phase 0.

---

## 14. Architecture Principles

The following principles govern NMS-WHITE development:

1. LibreNMS remains the monitoring engine.
2. NMS-WHITE is the product/application layer.
3. Prefer extension mechanisms over unnecessary LibreNMS core modifications.
4. Keep NMS-WHITE-specific database data separate where practical.
5. Maintain one application codebase for Docker and native deployment.
6. Use Docker as the primary development and initial deployment target.
7. Maintain explicit LibreNMS upstream tracking.
8. Protect the stable `master` branch.
9. Use `develop` for integration.
10. Use feature branches for individual changes.
11. Test upstream integrations before promotion.
12. Preserve GPLv3 licensing, copyright, and attribution requirements.
13. Do not build everything simultaneously.
14. Establish the monitoring foundation before higher-level product features.
15. Establish Docker production before building the native installer.
16. Establish tenant isolation before commercial multi-tenant deployment.

---

## 15. Phase 0 Scope

The goal of Phase 0 is to define the architecture and repository strategy before making major LibreNMS changes.

Phase 0 includes:

- Define NMS-WHITE branding direction
- Create the GitHub repository
- Establish LibreNMS upstream tracking
- Establish the development branch strategy
- Establish CI/CD strategy
- Document GPLv3 compliance
- Define application architecture
- Define database strategy
- Define Docker deployment strategy
- Define native deployment strategy

Expected output:

```text
NMS-WHITE repository
        +
LibreNMS upstream tracking
        +
Development workflow
        +
Architecture documentation
```

---

## 16. Phase 0 Exit Criteria

Phase 0 is complete when the following foundation is established:

- NMS-WHITE repository exists.
- `origin` points to the NMS-WHITE repository.
- `upstream` points to LibreNMS.
- `master` is protected.
- `develop` exists.
- Feature branch workflow exists.
- Upstream synchronization process is documented.
- Application architecture is documented.
- Database strategy is documented.
- Docker strategy is documented.
- Native deployment strategy is documented.
- GPLv3 requirements are documented.
- CI/CD direction is documented.

Once these requirements are satisfied, the project can proceed to Phase 1.

---

## 17. Development Roadmap

NMS-WHITE should be developed in the following order:

```text
Phase 0
Foundation
   |
   v
Phase 1
LibreNMS Docker
   |
   v
Phase 2
NMS-WHITE Branding
   |
   v
Phase 3
NMS-WHITE Dashboard
   |
   v
Phase 4
Custom NMS-WHITE Modules
   |
   v
Phase 5
NMS-WHITE API
   |
   v
Phase 6
Multi-Tenant
   |
   v
Phase 7
Docker Production
   |
   v
Phase 8
Native Installer
   |
   v
Phase 9
Update System
   |
   v
Phase 10
Backup & Disaster Recovery
   |
   v
Phase 11
Security
   |
   v
Phase 12
Commercial Product
```

The immediate priority is:

```text
Phase 0
   +
Phase 1
```

Do not begin multi-tenancy or the native installer before the earlier foundation is stable.

---

## 18. Immediate Next Step

After Phase 0 is completed, begin Phase 1.

The first operational target is:

```text
Ubuntu
  |
Docker
  |
LibreNMS
  |
First device
  |
SNMP
  |
Polling
  |
Graphs
  |
Alerts
```

The LibreNMS monitoring environment must be working before the NMS-WHITE customization layer becomes the primary development focus.

---

## 19. Summary

NMS-WHITE separates the monitoring engine from the product experience.

```text
LibreNMS
    =
Monitoring Engine
```

```text
NMS-WHITE
    =
Product / Application Layer
```

The repository model is:

```text
RND-NoriTech/NMS-WHITE-V2
          |
          +-- origin
          |
          +-- upstream
                |
                +-- librenms/librenms
```

The development model is:

```text
upstream/master
       |
       v
     master
       |
       v
    develop
       |
       v
   feature/*
```

The deployment model is:

```text
             NMS-WHITE SOURCE
                    |
          +---------+---------+
          |                   |
       Docker               Native
          |                   |
          +---------+---------+
                    |
             SAME APPLICATION
```

The project should progress incrementally:

```text
Foundation
    |
Monitoring
    |
Branding
    |
Dashboard
    |
Modules
    |
API
    |
Multi-Tenancy
    |
Production
    |
Native
    |
Updates
    |
Backup / DR
    |
Security
    |
Commercial Product
```

The immediate goal is to complete the foundation and establish a working LibreNMS Docker environment before proceeding to major NMS-WHITE customization.