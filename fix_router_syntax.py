import glob

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    orig = content
    
    # We replace the literal string "router.delete(`/admin/X/${id\n  })`)"
    # A simple string replace loop for the possible paths
    paths = ['projects', 'certifications', 'gallery', 'skills', 'contracts', 'timeline', 'skills/categories']
    
    for path in paths:
        bad_syntax1 = f"router.delete(`/admin/{path}/${{id\n  }})`)"
        bad_syntax2 = f"router.delete(`/admin/{path}/${{id\n    }})`)"
        good_syntax = f"router.delete(`/admin/{path}/${{id}}`)"
        
        content = content.replace(bad_syntax1, good_syntax)
        content = content.replace(bad_syntax2, good_syntax)
    
    if content != orig:
        with open(f, 'w') as file:
            file.write(content)
        print("Fixed router syntax in", f)

