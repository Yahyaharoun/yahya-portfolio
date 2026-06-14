import os
import re

# Pages à migrer vers AuthenticatedLayout
pages = {
    'resources/js/Pages/Admin/Certifications/Index.vue': ('Certifications', 'Gestion des Certifications'),
    'resources/js/Pages/Admin/Contracts/Index.vue': ('Contracts', 'Contrats & Partenariats'),
    'resources/js/Pages/Admin/Skills/Index.vue': ('Skills', 'Gestion des Compétences'),
    'resources/js/Pages/Admin/Projects/Index.vue': ('Projects', 'Gestion des Projets'),
    'resources/js/Pages/Admin/Timeline/Index.vue': ('Timeline', 'Parcours Professionnel'),
    'resources/js/Pages/Admin/Gallery/Index.vue': ('Gallery', 'Galerie Médias'),
}

for filepath, (page_name, title) in pages.items():
    with open(filepath, 'r') as f:
        content = f.read()

    if 'AuthenticatedLayout' in content:
        print(f"[SKIP] {filepath} already uses AuthenticatedLayout")
        continue

    # 1. Add import in <script setup>
    if "import AuthenticatedLayout" not in content:
        content = content.replace(
            "import { Head",
            "import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'\nimport { Head"
        )

    # 2. Find the outer template div (after <template>) and the nav block to remove
    # Strategy: wrap main content in AuthenticatedLayout, remove the nav block

    # Find the <div class="min-h-screen..."> wrapper start
    # and the </div>\n</template> end

    # Replace the outer wrapper start
    # Pattern: <template>\n  <Head ... />\n\n  <div class="min-h-screen ...">
    content = re.sub(
        r'(<template>\n)(\s*<Head[^/]*/>\n)\n(\s*<div class="min-h-screen[^"]*")',
        r'\1\2\n  <AuthenticatedLayout title="' + title + r'">\n\3',
        content
    )

    # Remove the <nav ... to </nav> block (the old top nav)
    content = re.sub(
        r'\n?    <nav class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700[^>]*>.*?</nav>\n',
        '\n',
        content,
        flags=re.DOTALL
    )

    # Remove the outer min-h-screen div opening (we keep the inner <main>)
    content = re.sub(
        r'  <div class="min-h-screen[^"]*">\n\n',
        '',
        content
    )

    # Fix closing: the last </div>\n</template> should become </AuthenticatedLayout>\n</template>
    # Count to find the right closing div
    content = re.sub(
        r'  </div>\n</template>',
        '</AuthenticatedLayout>\n</template>',
        content
    )

    with open(filepath, 'w') as f:
        f.write(content)
    print(f"[OK] Migrated {filepath}")

print("Done!")
