# Huma·log — Features à développer

Gap entre le design complet (landing + admin + staff + onboarding) et le code actuel.
On dev **une feature à la fois**, on valide avant de passer à la suivante. Le design/thème vient **en dernier**.

Légende : ✅ existe · 🟡 partiel · ❌ manquant

---

## Socle déjà en place
- Modèles : `Company` (tenant), `Employee`, `Punch` (append-only + hash chain), `PunchDevice`, `PunchCredential`, `User` (2FA)
- Panel Filament `app` (multi-tenant sur `Company`)
- `EmployeeResource` (list/create/edit + RelationManager credentials)
- `PunchDeviceResource`
- Kiosk de pointage (Livewire, auth par token borne)
- Auth Fortify (login / register / forgot / 2FA)

---

## Admin panel (PME) — `/app`

- [x] **1. HistoryResource** — historique pointages + création manuelle ✅ _(append-only : pas d'édition)_
- [x] **2. UserJobResource** — postes (page Manage : liste + create/edit/delete modales) ✅ _(modèle `UserJob`, FK `employees.user_job_id`)_
- [x] **3. UserScheduleResource** — plannings (List/Create/Edit + créneaux en RelationManager + action Publier) ✅ _(modèles `UserSchedule` + `ScheduleShift`)_ · grille hebdo drag-drop = phase design
- [x] **4. OffPeriodeResource** — absences + validation manager (Approuver/Refuser horodaté) ✅ _(modèle `OffPeriode`, jours ouvrés calculés)_
- [x] **5. Dashboard** — widgets stats + bar chart pointages/jour + table derniers pointages ✅ _(scopés tenant)_ · activité temps réel = phase design
- [x] **6. CompanySettings** — `EditTenantProfile` : identité (nom/raison sociale/SIRET/APE/adresse/tél/email) + fuseau ✅ · branding/sites/facturation/règles/danger = reportés
- [x] **7. Récap PDF** — page filtres période → PDF téléchargeable (dompdf) des heures/employé ✅ · heures sup./preview/historique/email = reportés

## Staff panel (super-admin interne) — panel entier ❌

- [x] **8. Panel `staff`** — 2e panel Filament à `/staff`, sans tenancy, accès réservé `users.is_staff` ✅
- [x] **9. CompanyResource** — liste globale entreprises + nb employés/users + create/edit ✅ · MRR/health/impersonation reportés
- [x] **10. LeadResource** — CRM leads (funnel new→contacté→qualifié→gagné/perdu) + filtres source/statut ✅ _(modèle `Lead`)_
- [x] **11. PageResource** — CMS (RichEditor + slug auto + SEO + OG image + brouillon/publié + action Publier) ✅ _(modèle `Page`)_ · rendu public = landing
- [x] **12. UserResource** — gestion users (staff flag hors fillable via forceFill, password optionnel en édition, rattachement entreprises, révocation 2FA) ✅ · matrice droits RBAC + sessions actives reportés

## Public / Onboarding

- [x] **13. Wizard onboarding** — `RegisterCompany` en Wizard : Entreprise (nom+slug) / Détails (secteur+taille) / Lancement ✅ · étape Équipe = feature 14 · routing par slug reporté
- [x] **14. Invitations équipe** — modèle `Invitation` + email + flux d'acceptation + rôle sur pivot + étape Équipe du wizard + InvitationResource (app) ✅
- [x] **15. Import CSV employés** — service synchrone (mapping par en-tête, validation ligne à ligne, rapport) + action sur la liste Employés ✅
- [x] **16. OAuth Google** — Socialite : redirect/callback, création/liaison par email, bouton sur login (render hook) ✅ · Apple reporté
- [x] **17. Landing page marketing** — structure PAS complète (nav/hero/problème/comparatif/solution/avis/tarifs/CTA/FAQ/footer) + modale contact → Lead ✅ · Alpine via Livewire · polish visuel = phase 19

## Design (phase finale)

- [x] **18. Thème Filament custom** — vert #2FA875 (Color::hex) + overrides fi-* (Geist, pills, radius 16) sur app + staff ✅ · login split-brand = phase 19
- [x] **19. Habillage** — landing (Geist + mockup hero) + auth split-brand Huma·log + password strength meter (register) ✅

---

## Ordre de dev proposé
Data d'abord, cosmétique en dernier :
`1 → 2 → 3 → 4 → 5 → 6 → 7 → 8-12 (staff) → 13-16 (onboarding) → 17 (landing) → 18-19 (design)`
