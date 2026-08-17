# Graph Report - ProjectManagementTool  (2026-08-17)

## Corpus Check
- 39 files · ~10,359 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 174 nodes · 152 edges · 37 communities (32 shown, 5 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 1 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c3c43b0e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- scripts
- composer.json
- Mermaid AI Skills
- User.php
- devDependencies
- require-dev
- package.json
- LARAVEL_README.md
- config
- AppServiceProvider
- UserFactory
- psr-4
- Pest.php
- Controller.php
- CLAUDE.md
- copilot-instructions.md

## God Nodes (most connected - your core abstractions)
1. `require-dev` - 10 edges
2. `scripts` - 9 edges
3. `Mermaid AI Skills` - 7 edges
4. `setup` - 7 edges
5. `VS Code Commands` - 6 edges
6. `User` - 6 edges
7. `config` - 5 edges
8. `AppServiceProvider` - 4 edges
9. `require` - 4 edges
10. `psr-4` - 4 edges

## Surprising Connections (you probably didn't know these)
- None detected - all connections are within the same source files.

## Import Cycles
- None detected.

## Communities (37 total, 5 thin omitted)

### Community 0 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 1 - "composer.json"
Cohesion: 0.10
Nodes (20): autoload-dev, psr-4, description, extra, laravel, keywords, dont-discover, license (+12 more)

### Community 2 - "Mermaid AI Skills"
Cohesion: 0.15
Nodes (12): Diagram editing & preview, Docs, Generate diagrams (GitHub Copilot required), Install / update this pack, LM Tools — call these for every diagram interaction, Mermaid AI Skills, Mermaid Chart cloud, @mermaid-chart slash commands (+4 more)

### Community 3 - "User.php"
Cohesion: 0.24
Nodes (7): User, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 4 - "devDependencies"
Cohesion: 0.18
Nodes (11): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+3 more)

### Community 5 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+2 more)

### Community 6 - "package.json"
Cohesion: 0.20
Nodes (9): @laravel/multiplex, optionalDependencies, @laravel/multiplex, private, $schema, scripts, build, dev (+1 more)

### Community 7 - "LARAVEL_README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 8 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 10 - "UserFactory"
Cohesion: 0.47
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 11 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

## Knowledge Gaps
- **78 isolated node(s):** `Mermaid Diagrams`, `Workflow`, `LM Tools — call these for every diagram interaction`, `Diagram editing & preview`, `Generate diagrams (GitHub Copilot required)` (+73 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **5 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `scripts` connect `scripts` to `composer.json`?**
  _High betweenness centrality (0.090) - this node is a cross-community bridge._
- **Why does `require-dev` connect `require-dev` to `composer.json`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **Why does `config` connect `config` to `composer.json`?**
  _High betweenness centrality (0.026) - this node is a cross-community bridge._
- **What connects `Mermaid Diagrams`, `Workflow`, `LM Tools — call these for every diagram interaction` to the rest of the system?**
  _78 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `scripts` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.09523809523809523 - nodes in this community are weakly interconnected._