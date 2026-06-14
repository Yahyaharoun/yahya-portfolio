with open('resources/js/Pages/Home.vue', 'r') as f:
    content = f.read()

if '<AlertModal' not in content:
    # We need to insert it right before </GuestLayout>
    injection = """
    <!-- Alert Modal -->
    <AlertModal 
      :show="alertState.show" 
      :message="alertState.message" 
      @close="alertState.show = false" 
    />
  </GuestLayout>
"""
    content = content.replace('  </GuestLayout>', injection)
    
    with open('resources/js/Pages/Home.vue', 'w') as f:
        f.write(content)
    print("Injected AlertModal into Home.vue template!")
else:
    print("AlertModal already in template.")
