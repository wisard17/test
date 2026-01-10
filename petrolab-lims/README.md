# Petrolab LIMS (Yii2 Advanced Skeleton)

This folder contains a runnable skeleton for Petrolab LIMS using Yii2 Advanced layout. It focuses on database migrations, ActiveRecord models, RBAC seeding, audit behavior, and a few key backend controllers/views so you can bootstrap the application quickly.

## Structure
- `common/models`: ActiveRecord models for core entities.
- `common/components`: shared behaviors/components (audit log).
- `common/rbac`: initial RBAC seeder.
- `console/migrations`: Yii2 migrations for schema.
- `backend/controllers`: sample CRUD controllers (samples, sample requests, tests, results, CoA).
- `backend/views`: simple views with GridView and forms.

To use, copy these files into a fresh Yii2 Advanced project, adjust namespaces if your app id differs, then run `php yii migrate` and `php yii rbac/init` (see rbac seeder class).
