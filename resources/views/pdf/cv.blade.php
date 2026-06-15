<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV {{ $admin ? strtoupper($admin->name) : 'YAHYA HAROUN' }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333333;
            background-color: #ffffff;
            margin: 0;
        }

        /* Layout */
        .page {
            padding: 40px;
        }

        /* Colors */
        .text-primary { color: #2d3748; }
        .text-accent { color: #6b46c1; /* Violet */ }
        .text-muted { color: #718096; }
        .bg-light { background-color: #f7fafc; }

        /* Typography */
        h1, h2, h3 { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        h1 {
            font-size: 28pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: #1a202c;
        }
        .subtitle {
            font-size: 14pt;
            font-weight: 600;
            color: #6b46c1;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            color: #2d3748;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        h3 {
            font-size: 12pt;
            font-weight: bold;
            color: #2d3748;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .contact-info {
            font-size: 10pt;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .contact-info span {
            margin: 0 8px;
        }
        .contact-info a {
            color: #6b46c1;
            text-decoration: none;
        }

        /* Profile Summary */
        .summary {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 25px;
        }

        /* Experience Section */
        .experience-item {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .experience-header {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .experience-title {
            display: table-cell;
            font-weight: bold;
            font-size: 12pt;
            color: #2d3748;
        }
        .experience-date {
            display: table-cell;
            text-align: right;
            font-size: 10pt;
            color: #718096;
            font-weight: 600;
        }
        .experience-company {
            font-weight: 600;
            color: #6b46c1;
            margin-bottom: 5px;
            font-size: 11pt;
        }
        .experience-description {
            font-size: 10.5pt;
            margin-bottom: 5px;
            text-align: justify;
        }

        /* Skills Section */
        .skills-container {
            width: 100%;
        }
        .skill-category {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .skill-category-title {
            font-weight: bold;
            font-size: 11pt;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .skill-list {
            font-size: 10.5pt;
            color: #2d3748;
        }
        .skill-badge {
            display: inline-block;
            background-color: #edf2f7;
            color: #4a5568;
            padding: 3px 8px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 9.5pt;
            border: 1px solid #e2e8f0;
        }

        /* Education & Certifications */
        .education-item {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .education-title {
            font-weight: bold;
            font-size: 11pt;
            color: #2d3748;
        }
        .education-meta {
            font-size: 10pt;
            color: #718096;
        }

        /* Grid System for Education/Certifications */
        .grid-half {
            width: 48%;
            float: left;
        }
        .grid-right {
            float: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <h1>{{ $admin ? $admin->name : 'YAHYA HAROUN' }}</h1>
            <div class="subtitle">{{ $admin && $admin->title ? $admin->title : 'Ingénieur Logiciel & Entrepreneur Tech' }}</div>
            
            <div class="contact-info">
                @if($admin && $admin->phone)
                    <span>📞 {{ $admin->phone }}</span> | 
                @else
                    <span>📞 +237 690722465</span> | 
                @endif
                
                @if($admin && $admin->email)
                    <span>✉️ <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a></span>
                @else
                    <span>✉️ <a href="mailto:Yahyaharoun.657@gmail.com">Yahyaharoun.657@gmail.com</a></span>
                @endif
            </div>

            <div class="contact-info">
                <span>📍 Cameroun</span>
                @if($admin && $admin->linkedin_url)
                    | <span>🔗 <a href="{{ $admin->linkedin_url }}">LinkedIn</a></span>
                @endif
                @if($admin && $admin->github_url)
                    | <span>💻 <a href="{{ $admin->github_url }}">GitHub</a></span>
                @endif
            </div>
        </div>

        <!-- PROFILE SUMMARY -->
        <h2>Profil</h2>
        <div class="summary">
            Passionné par l'innovation technologique et l'entrepreneuriat, je conçois et développe des applications web robustes, scalables et orientées utilisateur. Fort d'une expertise couvrant le développement Full-Stack (Laravel, Vue.js), l'architecture logicielle et l'intégration continue, je m'engage à fournir des solutions techniques modernes (PWA, API, Cloud) répondant aux plus hauts standards de qualité et de performance.
        </div>

        <!-- EXPERIENCES -->
        @if(isset($experiences) && $experiences->count() > 0)
        <h2>Expériences Professionnelles</h2>
        <div>
            @foreach($experiences as $exp)
            <div class="experience-item">
                <div class="experience-header">
                    <div class="experience-title">{{ $exp->title }}</div>
                    <div class="experience-date">
                        {{ \Carbon\Carbon::parse($exp->date_start)->format('M Y') }} - 
                        {{ $exp->date_end ? \Carbon\Carbon::parse($exp->date_end)->format('M Y') : 'Présent' }}
                    </div>
                </div>
                <div class="experience-company">{{ $exp->company }}</div>
                <div class="experience-description">
                    {!! nl2br(e($exp->description)) !!}
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- SKILLS -->
        @if(isset($skillCategories) && $skillCategories->count() > 0)
        <h2>Compétences Techniques</h2>
        <div class="skills-container clearfix">
            @foreach($skillCategories as $category)
            <div class="skill-category">
                <div class="skill-category-title">{{ $category->name }}</div>
                <div class="skill-list">
                    @foreach($category->skills as $skill)
                        <span class="skill-badge">{{ $skill->name }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="clearfix">
            <!-- DIPLOMAS -->
            @if(isset($diplomas) && $diplomas->count() > 0)
            <div class="grid-half">
                <h2>Formations</h2>
                @foreach($diplomas as $diploma)
                <div class="education-item">
                    <div class="education-title">{{ $diploma->title }}</div>
                    <div class="education-meta">{{ $diploma->institution }} | {{ $diploma->year }}</div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- CERTIFICATIONS -->
            @if(isset($certifications) && $certifications->count() > 0)
            <div class="grid-half grid-right">
                <h2>Certifications</h2>
                @foreach($certifications as $cert)
                <div class="education-item">
                    <div class="education-title">{{ $cert->title }}</div>
                    <div class="education-meta">{{ $cert->issuer }} | {{ \Carbon\Carbon::parse($cert->issued_at)->format('Y') }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
    </div>
</body>
</html>
