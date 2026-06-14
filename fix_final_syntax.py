import glob
import re

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    orig = content
    
    # We want to match:
    # confirmAction('...', () => {
    #   router.delete(`/admin/X/${id}`)
    # }
    # }
    # and replace the first } with })
    
    content = re.sub(r"(confirmAction\('.*?', \(\) => \{\s*router\.delete\(`.*?`\)\s*)\}", r"\1})", content)
    
    if content != orig:
        with open(f, 'w') as file:
            file.write(content)
        print("Fixed final syntax in", f)

