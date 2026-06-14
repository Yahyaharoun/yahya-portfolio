const fs = require('fs');
const glob = require('glob');

const files = glob.sync('resources/js/Pages/Admin/**/*.vue');

const backButtonHtml = `
      <div class="flex items-center gap-4">
        <Link href="/" class="text-slate-500 hover:text-violet-500 flex items-center gap-1" title="Retour au site">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
        </Link>
        <Link href="/admin/dashboard" class="font-bold text-xl text-violet-500">AdminPanel</Link>
      </div>`;

const backButtonHtmlDiv = `
      <div class="flex items-center gap-4">
        <Link href="/" class="text-slate-500 hover:text-violet-500 flex items-center gap-1" title="Retour au site">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
        </Link>
        <div class="font-bold text-xl text-violet-500">AdminPanel</div>
      </div>`;

files.forEach(file => {
  let content = fs.readFileSync(file, 'utf8');
  let original = content;

  // For Index.vue pages that use <Link href="/admin/dashboard">
  content = content.replace(
    /<Link href="\/admin\/dashboard" class="font-bold text-xl text-violet-500">AdminPanel<\/Link>/g,
    backButtonHtml.trim()
  );

  // For Dashboard.vue that uses <div class="font-bold text-xl text-violet-500">AdminPanel</div>
  content = content.replace(
    /<div class="font-bold text-xl text-violet-500">AdminPanel<\/div>/g,
    backButtonHtmlDiv.trim()
  );

  if (content !== original) {
    fs.writeFileSync(file, content);
    console.log('Updated ' + file);
  }
});
