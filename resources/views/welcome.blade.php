<!doctype html>
<html lang="ar" dir="rtl">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نظام إدارة الموارد المؤسسية</title>
  <script src="/_sdk/element_sdk.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&amp;display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
        body {
            box-sizing: border-box;
        }
        
        * {
            font-family: 'Tajawal', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
        }
        
        .input-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
 </head>
 <body class="h-full w-full overflow-auto">
  <div class="min-h-full w-full gradient-bg"><!-- Header -->
   <header class="glass-effect border-b border-white/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
     <div class="flex justify-between items-center">
      <div class="flex items-center gap-3">
       <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
       </div>
       <div>
        <h1 id="systemTitle" class="text-2xl font-bold text-white">نظام ERP</h1>
        <p id="systemSubtitle" class="text-sm text-white/80">إدارة الموارد المؤسسية</p>
       </div>
      </div>
      <nav class="flex gap-3" id="mainNav"><!-- Will be populated by JavaScript based on auth status -->
      </nav>
     </div>
    </div>
   </header><!-- Main Content -->
   <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"><!-- Welcome Section -->
    <div id="welcomeSection" class="text-center mb-16">
     <div class="floating-animation inline-block mb-6">
      <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
       <svg class="w-10 h-10 text-white" fill="currentColor" viewbox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
       </svg>
      </div>
     </div>
     <h2 id="heroTitle" class="text-5xl md:text-6xl font-extrabold text-white mb-4">مرحباً بك في نظام ERP</h2>
     <p id="heroSubtitle" class="text-xl text-white/90 max-w-2xl mx-auto mb-12">حل متكامل لإدارة موارد مؤسستك بكفاءة وسهولة</p><!-- CTA Buttons -->
     <div class="flex gap-4 justify-center flex-wrap"> <a href="#features" class="px-8 py-4 bg-white/20 text-white rounded-xl font-bold text-lg hover:bg-white/30 transition-all backdrop-blur-sm"> اكتشف المزيد </a>
     </div>
    </div><!-- Features Section -->
    <div id="features" class="grid md:grid-cols-3 gap-8 mt-20">
     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 card-hover border border-white/20">
      <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center mb-6">
       <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
       </svg>
      </div>
      <h3 class="text-2xl font-bold text-white mb-3">إدارة مالية محكمة</h3>
      <p class="text-white/80">تتبع دقيق لجميع العمليات المالية والحسابات بسهولة</p>
     </div>
     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 card-hover border border-white/20">
      <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center mb-6">
       <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
       </svg>
      </div>
      <h3 class="text-2xl font-bold text-white mb-3">إدارة الموظفين</h3>
      <p class="text-white/80">نظام شامل لإدارة الموارد البشرية والرواتب</p>
     </div>
     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 card-hover border border-white/20">
      <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center mb-6">
       <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
       </svg>
      </div>
      <h3 class="text-2xl font-bold text-white mb-3">إدارة المخزون</h3>
      <p class="text-white/80">تحكم كامل في المخزون والمشتريات والمبيعات</p>
     </div>
    </div>
   </main><!-- Footer -->
   <footer class="glass-effect border-t border-white/20 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
     <p class="text-center text-white/70 text-sm">©Made with ❤️ by <a href="https://www.facebook.com/share/1G8MceLtjR/" target="_blank" class="fw-semibold">Ahmed Abd-Alkarim 2026</a></p>
    </div>
   </footer>
  </div>
  <script>
        const defaultConfig = {
            system_title: "نظام ERP",
            system_subtitle: "إدارة الموارد المؤسسية",
            hero_title: "مرحباً بك في نظام ERP",
            hero_subtitle: "حل متكامل لإدارة موارد مؤسستك بكفاءة وسهولة"
        };

        // Check if user is logged in using Laravel auth
        function checkAuthStatus() {
            const isLoggedIn = @json(auth()->check());
            
            const navElement = document.getElementById('mainNav');
            
            if (isLoggedIn) {
                // إذا كان مسجل دخول - اظهر لوحة التحكم
                navElement.innerHTML = `
                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-white text-purple-600 rounded-lg font-bold hover:bg-white/90 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                        </svg>
                        لوحة التحكم
                    </a>
                `;
            } else {
                // إذا لم يكن مسجل دخول - اظهر أزرار التسجيل
                @if (Route::has('login'))
                    navElement.innerHTML = `
                        <a href="{{ route('login') }}" class="px-6 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-white/90 transition-all">
                            تسجيل الدخول
                        </a>
                    `;
                @endif
            }
        }

        async function onConfigChange(config) {
            document.getElementById('systemTitle').textContent = config.system_title || defaultConfig.system_title;
            document.getElementById('systemSubtitle').textContent = config.system_subtitle || defaultConfig.system_subtitle;
            document.getElementById('heroTitle').textContent = config.hero_title || defaultConfig.hero_title;
            document.getElementById('heroSubtitle').textContent = config.hero_subtitle || defaultConfig.hero_subtitle;
        }

        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig,
                onConfigChange,
                mapToCapabilities: (config) => ({
                    recolorables: [],
                    borderables: [],
                    fontEditable: undefined,
                    fontSizeable: undefined
                }),
                mapToEditPanelValues: (config) => new Map([
                    ["system_title", config.system_title || defaultConfig.system_title],
                    ["system_subtitle", config.system_subtitle || defaultConfig.system_subtitle],
                    ["hero_title", config.hero_title || defaultConfig.hero_title],
                    ["hero_subtitle", config.hero_subtitle || defaultConfig.hero_subtitle]
                ])
            });
        }

        // Initialize page
        onConfigChange(defaultConfig);
        checkAuthStatus();
    </script>
 <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9ba0f360701cd821',t:'MTc2Nzc2MjY0Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>