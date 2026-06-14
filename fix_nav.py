import glob

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

back_html = """      <div class="flex items-center gap-4">
        <Link href="/" class="text-slate-500 hover:text-violet-500 flex items-center gap-1" title="Retour au site">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
        </Link>
        <Link href="/admin/dashboard" class="font-bold text-xl text-violet-500">AdminPanel</Link>
      </div>"""

back_html_div = """      <div class="flex items-center gap-4">
        <Link href="/" class="text-slate-500 hover:text-violet-500 flex items-center gap-1" title="Retour au site">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
        </Link>
        <div class="font-bold text-xl text-violet-500">AdminPanel</div>
      </div>"""

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    orig = content
    content = content.replace(
        '<Link href="/admin/dashboard" class="font-bold text-xl text-violet-500">AdminPanel</Link>',
        back_html
    )
    content = content.replace(
        '<div class="font-bold text-xl text-violet-500">AdminPanel</div>',
        back_html_div
    )
    
    if content != orig:
        with open(f, 'w') as file:
            file.write(content)
        print("Updated", f)
