import os
import re

files = [
    'resources/js/Pages/Admin/Projects/Index.vue',
    'resources/js/Pages/Admin/Certifications/Index.vue',
    'resources/js/Pages/Admin/Skills/Index.vue',
    'resources/js/Pages/Admin/Timeline/Index.vue',
    'resources/js/Pages/Admin/Gallery/Index.vue',
    'resources/js/Pages/Admin/Contracts/Index.vue',
    'resources/js/Pages/Admin/Diplomas/Index.vue',
]

# Fix 1: input/select/textarea without dark text color
def fix_inputs(content):
    # Add dark:text-slate-100 dark:placeholder-slate-400 to all inputs/selects/textareas
    # that have dark:bg-slate-900 but lack dark:text-slate-100
    content = re.sub(
        r'(class="[^"]*dark:bg-slate-900[^"]*)"',
        lambda m: m.group(0) if 'dark:text-slate' in m.group(0) 
                  else m.group(1) + ' text-slate-900 dark:text-slate-100"',
        content
    )
    return content

# Fix 2: Wrap cards/forms that use bg-white (light only) to include dark text
def fix_cards(content):
    content = re.sub(
        r'(class="[^"]*bg-white dark:bg-slate-800[^"]*)"',
        lambda m: m.group(0) if 'text-slate' in m.group(0)
                  else m.group(1) + ' text-slate-900 dark:text-slate-100"',
        content
    )
    return content

# Fix 3: Labels without text color  
def fix_labels(content):
    # label elements with "text-sm font-medium" but no dark: color
    content = re.sub(
        r'(<label class="block text-sm font-medium mb-1")>',
        r'<label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">',
        content
    )
    content = re.sub(
        r'(<label class="block text-sm font-medium mb-2")>',
        r'<label class="block text-sm font-medium mb-2 text-slate-700 dark:text-slate-300">',
        content
    )
    return content

# Fix 4: h2/h3 headings
def fix_headings(content):
    content = re.sub(
        r'(<h2 class="text-2xl font-semibold mb-6")>',
        r'<h2 class="text-2xl font-semibold mb-6 text-slate-100">',
        content
    )
    content = re.sub(
        r'(<h3 class="text-lg font-medium mb-4")>',
        r'<h3 class="text-lg font-medium mb-4 text-slate-100">',
        content
    )
    content = re.sub(
        r'(<h3 class="text-lg font-semibold mb-4")>',
        r'<h3 class="text-lg font-semibold mb-4 text-slate-100">',
        content
    )
    return content

# Fix 5: main wrapper - remove unnecessary padding wrapper added by migration
def fix_main_wrapper(content):
    # The AuthenticatedLayout already has p-4 sm:p-6 lg:p-8
    # Remove extra <main class="max-w-7xl mx-auto py-10 px-4 ..."> if present
    content = re.sub(
        r'\n  <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">\n',
        '\n  <div class="max-w-7xl mx-auto space-y-6">\n',
        content
    )
    content = re.sub(
        r'\n  </main>\n\n  <Confirm',
        '\n  </div>\n\n  <Confirm',
        content
    )
    content = re.sub(
        r'\n  </main>\n\n</AuthenticatedLayout>',
        '\n  </div>\n\n</AuthenticatedLayout>',
        content
    )
    return content

for filepath in files:
    if not os.path.exists(filepath):
        print(f"[SKIP] {filepath} not found")
        continue
    with open(filepath, 'r') as f:
        content = f.read()
    
    original = content
    content = fix_inputs(content)
    content = fix_cards(content)
    content = fix_labels(content)
    content = fix_headings(content)
    content = fix_main_wrapper(content)
    
    if content != original:
        with open(filepath, 'w') as f:
            f.write(content)
        print(f"[OK] Fixed: {filepath}")
    else:
        print(f"[--] No changes: {filepath}")

print("Done!")
