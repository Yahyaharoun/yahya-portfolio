<script setup lang="ts">
/**
 * Example Page: Public Portfolio Home
 * Uses GuestLayout + Timeline + MatrixSkills
 */
import GuestLayout from '@/Layouts/GuestLayout.vue'
import Timeline, { type TimelineItem } from '@/Components/Timeline.vue'
import MatrixSkills, { type SkillCategory } from '@/Components/MatrixSkills.vue'

// ── Timeline data ─────────────────────────────────────────────────────────────

const timelineItems: TimelineItem[] = [
  {
    id: 1,
    type: 'work',
    title: 'Lead Full-Stack Engineer',
    organization: 'Acme Corp',
    location: 'Casablanca, Morocco',
    dateStart: 'Jan 2023',
    current: true,
    description:
      'Architected and delivered a multi-tenant SaaS platform serving 50k+ users. Led a team of 6 engineers across backend (Laravel 11), frontend (Vue 3 + Inertia), and DevOps (Docker, CI/CD).',
    tags: ['Laravel', 'Vue 3', 'MySQL', 'Redis', 'Docker', 'AWS'],
    link: 'https://acmecorp.com',
  },
  {
    id: 2,
    type: 'education',
    title: "Master's in Computer Science — Cybersecurity",
    organization: 'Université Mohammed V',
    location: 'Rabat, Morocco',
    dateStart: 'Sep 2020',
    dateEnd: 'Jul 2022',
    description:
      'Specialized in network security, cryptography, and secure software development. Thesis: "Zero-Trust Architecture for Microservices at Scale".',
    tags: ['Cryptography', 'Network Security', 'PKI', 'Zero Trust'],
  },
  {
    id: 3,
    type: 'work',
    title: 'Backend Engineer',
    organization: 'TechStartup SA',
    location: 'Remote',
    dateStart: 'Mar 2019',
    dateEnd: 'Dec 2022',
    description:
      'Built RESTful APIs and real-time WebSocket services powering a logistics platform. Reduced p95 API latency from 800ms to 120ms via Redis caching and query optimization.',
    tags: ['PHP', 'Laravel', 'PostgreSQL', 'WebSockets'],
  },
  {
    id: 4,
    type: 'award',
    title: '1st Place — National Hackathon',
    organization: 'Morocco Tech Summit',
    location: 'Marrakech, Morocco',
    dateStart: 'Oct 2021',
    description:
      'Won first place among 120 teams for a real-time supply-chain traceability system built with blockchain and IoT sensors.',
    tags: ['Blockchain', 'IoT', 'Laravel', 'Vue'],
  },
  {
    id: 5,
    type: 'education',
    title: "Bachelor's in Business Engineering",
    organization: 'ENCG Casablanca',
    location: 'Casablanca, Morocco',
    dateStart: 'Sep 2016',
    dateEnd: 'Jun 2019',
    description:
      'Dual curriculum combining software engineering fundamentals with business strategy, entrepreneurship, and financial modeling.',
    tags: ['Entrepreneurship', 'Finance', 'Strategy', 'Java'],
  },
]

// ── Skills data ───────────────────────────────────────────────────────────────

const skillCategories: SkillCategory[] = [
  {
    key: 'webdev',
    label: 'Web Development',
    description: 'Full-stack engineering from API to pixel-perfect UI.',
    skills: [
      { id: 'wd-1', name: 'Laravel / PHP 8.3+', level: 'expert',       proficiency: 95, icon: '🐘', tags: ['REST', 'Queues', 'Octane'] },
      { id: 'wd-2', name: 'Vue.js 3 + Inertia',  level: 'expert',       proficiency: 92, icon: '💚', tags: ['Composition API', 'SSR']  },
      { id: 'wd-3', name: 'TypeScript',            level: 'advanced',     proficiency: 85, icon: '🔷'                                   },
      { id: 'wd-4', name: 'MySQL / PostgreSQL',    level: 'advanced',     proficiency: 88, icon: '🗄️', tags: ['Query optimization']     },
      { id: 'wd-5', name: 'Redis',                 level: 'advanced',     proficiency: 82, icon: '⚡'                                   },
      { id: 'wd-6', name: 'Tailwind CSS',          level: 'expert',       proficiency: 90, icon: '🎨'                                   },
    ],
  },
  {
    key: 'cybersecurity',
    label: 'Cybersecurity',
    description: 'OWASP, threat modelling, and secure architecture.',
    skills: [
      { id: 'cs-1', name: 'OWASP / Secure Dev',   level: 'advanced',     proficiency: 88, icon: '🛡️' },
      { id: 'cs-2', name: 'Penetration Testing',   level: 'intermediate', proficiency: 70, icon: '🔍' },
      { id: 'cs-3', name: 'Cryptography (PKI)',     level: 'advanced',     proficiency: 80, icon: '🔐' },
      { id: 'cs-4', name: 'Network Security',       level: 'advanced',     proficiency: 78, icon: '🌐' },
      { id: 'cs-5', name: 'Zero Trust / IAM',       level: 'intermediate', proficiency: 72, icon: '🔑' },
    ],
  },
  {
    key: 'cloud',
    label: 'Cloud & DevOps',
    description: 'Scalable infrastructure, IaC, and CI/CD automation.',
    skills: [
      { id: 'cl-1', name: 'AWS (EC2, S3, RDS)',    level: 'advanced',     proficiency: 82, icon: '☁️', tags: ['CloudFront', 'Lambda'] },
      { id: 'cl-2', name: 'Docker / Compose',       level: 'expert',       proficiency: 90, icon: '🐳'                                },
      { id: 'cl-3', name: 'GitHub Actions CI/CD',   level: 'advanced',     proficiency: 85, icon: '⚙️'                                },
      { id: 'cl-4', name: 'Nginx / Load Balancing', level: 'advanced',     proficiency: 80, icon: '🌍'                                },
      { id: 'cl-5', name: 'Terraform (IaC)',         level: 'intermediate', proficiency: 65, icon: '🔧'                                },
    ],
  },
  {
    key: 'business',
    label: 'Business',
    description: 'Strategy, finance, and market analysis.',
    skills: [
      { id: 'bs-1', name: 'Business Strategy',     level: 'advanced',     proficiency: 85, icon: '📊' },
      { id: 'bs-2', name: 'Financial Modelling',   level: 'intermediate', proficiency: 72, icon: '📈' },
      { id: 'bs-3', name: 'Market Research',       level: 'advanced',     proficiency: 80, icon: '🔎' },
      { id: 'bs-4', name: 'Negotiation',            level: 'advanced',     proficiency: 83, icon: '🤝' },
    ],
  },
  {
    key: 'projectmanagement',
    label: 'Project Management',
    description: 'Agile, Scrum, and cross-functional leadership.',
    skills: [
      { id: 'pm-1', name: 'Agile / Scrum',        level: 'expert',       proficiency: 92, icon: '🔄' },
      { id: 'pm-2', name: 'Risk Management',       level: 'advanced',     proficiency: 80, icon: '⚠️' },
      { id: 'pm-3', name: 'JIRA / Linear',         level: 'expert',       proficiency: 88, icon: '📋' },
      { id: 'pm-4', name: 'Team Leadership',       level: 'advanced',     proficiency: 85, icon: '👥' },
    ],
  },
  {
    key: 'entrepreneurship',
    label: 'Entrepreneurship',
    description: 'Ideation, MVP, fundraising, and growth hacking.',
    skills: [
      { id: 'en-1', name: 'Lean Startup / MVP',   level: 'advanced',     proficiency: 85, icon: '🚀' },
      { id: 'en-2', name: 'Fundraising / Pitch',  level: 'intermediate', proficiency: 70, icon: '💰' },
      { id: 'en-3', name: 'Growth Hacking',        level: 'advanced',     proficiency: 78, icon: '📣' },
      { id: 'en-4', name: 'Product Vision',        level: 'expert',       proficiency: 90, icon: '🔭' },
    ],
  },
]
</script>

<template>
  <GuestLayout>
    <!-- ── Hero placeholder ── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
      <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-100 tracking-tight">
        Yahya <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Haroun</span>
      </h1>
      <p class="mt-6 text-xl text-slate-400 max-w-2xl mx-auto">
        Full-Stack Engineer · Cybersecurity · Cloud · Entrepreneur
      </p>
    </section>

    <!-- ── Skills Matrix ── -->
    <MatrixSkills
      :categories="skillCategories"
      title="Matrix de Compétences"
      subtitle="A full-spectrum skill set spanning engineering, security, cloud, and business strategy."
    />

    <!-- ── Timeline ── -->
    <Timeline
      :items="timelineItems"
      title="Parcours Professionnel"
      subtitle="A journey through technology, business and continuous growth."
    />
  </GuestLayout>
</template>
