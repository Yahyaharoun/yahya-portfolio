-- Supabase RLS Policy Configuration Script
-- Exécutez ce script dans l'éditeur SQL de Supabase (SQL Editor)

-- 1. CV Downloads Table
ALTER TABLE cv_downloads ENABLE ROW LEVEL SECURITY;

-- Politique pour les utilisateurs anonymes (visiteurs) : Seulement INSERT
CREATE POLICY "Allow anonymous inserts on cv_downloads"
ON cv_downloads
FOR INSERT 
TO anon
WITH CHECK (true);

-- Politique pour les administrateurs (authentifiés) : ALL
CREATE POLICY "Allow full access for authenticated users on cv_downloads"
ON cv_downloads
FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- 2. Partnerships (Contracts) Table
ALTER TABLE partnerships ENABLE ROW LEVEL SECURITY;

-- Politique pour les utilisateurs anonymes (visiteurs) : Seulement INSERT
CREATE POLICY "Allow anonymous inserts on partnerships"
ON partnerships
FOR INSERT 
TO anon
WITH CHECK (true);

-- Politique pour les administrateurs (authentifiés) : ALL
CREATE POLICY "Allow full access for authenticated users on partnerships"
ON partnerships
FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- 3. Visitors Table (Analytics)
ALTER TABLE visitors ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow anonymous inserts and selects on visitors"
ON visitors
FOR ALL 
TO anon
USING (true)
WITH CHECK (true);

CREATE POLICY "Allow full access for authenticated users on visitors"
ON visitors
FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- 4. Page Visits Table (Analytics)
ALTER TABLE page_visits ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow anonymous inserts, updates, and selects on page_visits"
ON page_visits
FOR ALL 
TO anon
USING (true)
WITH CHECK (true);

CREATE POLICY "Allow full access for authenticated users on page_visits"
ON page_visits
FOR ALL
TO authenticated
USING (true)
WITH CHECK (true);

-- 5. Contacts (if exists)
-- ALTER TABLE contacts ENABLE ROW LEVEL SECURITY;
-- CREATE POLICY "Allow anonymous inserts on contacts" ON contacts FOR INSERT TO anon WITH CHECK (true);
-- CREATE POLICY "Allow full access for authenticated on contacts" ON contacts FOR ALL TO authenticated USING (true) WITH CHECK (true);

-- Note: RLS forces explicitly defined policies to be the ONLY allowed operations.
-- If a table has RLS enabled but no policy, ALL operations are denied.
