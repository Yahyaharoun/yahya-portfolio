import glob
import re

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    orig = content
    
    # We need to find: router.delete(`/admin/path/${id\n  })`)
    # And replace it with: router.delete(`/admin/path/${id}`)
    
    # In regex: router\.delete\(`(.*?)id\n\s*\)\`\)
    
    content = re.sub(r"router\.delete\(`(.*?\$\{)id\n\s*\)\`\)", r"router.delete(`\1id}`)", content)
    
    if content != orig:
        with open(f, 'w') as file:
            file.write(content)
        print("Fixed syntax in", f)

