---
apply: always
---

DEVELOPMENT ENVIRONMENT
- Work on Windows via Cursor, but edit files on Debian server (Samba-mounted)
- Project paths: /volume_html/projectname
- Apache2 runs in Docker container
- Local URLs: http://project.debian (where "project" = project name)
- ALL commands (Composer, Artisan, npm, git) run in Debian bash, NOT Windows

TECH STACK
- PHP 8.x (PSR-12 coding standards)
- Laravel 10-12.x
- Filament 3.x, 4.x or 5.x for admin panels - BEWARE: check first in composer.json which version we are using!
- MySQL/MariaDB database
- Tailwind CSS (some projects use Bootstrap 4/5)
- Mailtrap for email testing

CODING PREFERENCES
- Prioritize readability over brevity
- Add short docblocks for complex logic
- Use lampminds/customization package when available in project (prefer this over creating new Filament code from scratch)
- If lampminds/customization not in use, use standard Laravel/Filament approach
- Always document in english, even if the project is for spanish people.
- Always create functions and scripts names in english.

PROGRAMMING TIPS
- When defining actions for the Filament index resource, group them all inside a group action, so they are all inside a 3-dot icon. Place the 3 dots at the beginning of the row, not at the end.
- When defining the index table(), use "return parent::table($table)..." instead of just "return $table..." so we inherit all the parent class.
- whenever a table has a "list_order" column, that means that we need to use the reorderable() Filament feature in the Filament index list, and that this column should not be editable nor visible in the edit form.
- In filament indexes, only add filters if explicitly commanded, and always place them on top of the index, always visible.
- In filament, whenever possible, build editing forms splitting data in tabs, and use 2 or 3 columns to better arrange the input fields without having to scroll.
- In filament, After a create or edit is performed, always return to the index page
- If any change is required in the Lampminds package, just give the directions. Never modify anything inside /vendor.

ASSISTANT BEHAVIOR
- Be concise but complete
- Explain "why" before "how"
- Provide commands for Debian bash (assume root user with sudo)
- Include full absolute paths when referencing config files
- Use correct Laravel artisan syntax for code generation
- English for documentation; Spanish for client communications/emails
- Make no asumptions. If you have doubts, ask me first.

GIT WORKFLOW
- Repos hosted on GitHub
- Edited using both Cursor and PHPStorm

