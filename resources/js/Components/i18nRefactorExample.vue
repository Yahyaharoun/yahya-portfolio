<!--
  ╔══════════════════════════════════════════════════════════════════╗
  ║  i18n Refactor Reference                                         ║
  ║  Demonstrates static → dynamic $t() migration patterns          ║
  ╚══════════════════════════════════════════════════════════════════╝
-->

<script setup lang="ts">
// ── Step 1: Import useI18n inside every component that displays strings ────
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// ── Step 2 (optional): Access locale reactively for conditional logic ──────
// import { useI18n } from 'vue-i18n'
// const { t, locale } = useI18n()
// Watch locale for component-level side-effects if needed.
</script>

<template>
  <!--
    ┌──────────────────────────────────────────────────────────────────────┐
    │  BEFORE (static, English-only)                                       │
    └──────────────────────────────────────────────────────────────────────┘

    <section id="skills">
      <h2>Skills Matrix</h2>
      <button>All</button>
      <button>Web Development</button>
      <span>Avg. proficiency</span>
      <span>skills</span>
    </section>

    <form>
      <label>Full Name</label>
      <input placeholder="e.g. Jane Smith" />
      <label>Phone Number</label>
      <input placeholder="e.g. +1 555 000 0000" />
      <button type="submit">Continue</button>
      <button type="button">Cancel</button>
    </form>
  -->

  <!--
    ┌──────────────────────────────────────────────────────────────────────┐
    │  AFTER (dynamic, locale-aware)                                       │
    └──────────────────────────────────────────────────────────────────────┘
  -->

  <!-- Section header ─────────────────────────────────────────────────── -->
  <section id="skills" :aria-label="t('sections.skills')">
    <h2>{{ t('sections.skills') }}</h2>

    <!-- Category filters -->
    <button :aria-pressed="true">{{ t('skills.filter_all') }}</button>
    <button>{{ t('skills.categories.web') }}</button>
    <button>{{ t('skills.categories.cyber') }}</button>
    <button>{{ t('skills.categories.cloud') }}</button>
    <button>{{ t('skills.categories.business') }}</button>
    <button>{{ t('skills.categories.pm') }}</button>
    <button>{{ t('skills.categories.entrepreneurship') }}</button>

    <!-- Stats labels -->
    <span>{{ t('skills.avg_proficiency') }}</span>
    <span>{{ t('skills.stat_categories') }}</span>
  </section>

  <!-- CV Tunnel form ──────────────────────────────────────────────────── -->
  <form>
    <!-- Dynamic step indicator using named interpolation -->
    <p>{{ t('cv_tunnel.step', { current: 1, total: 2 }) }}</p>

    <label for="full-name">{{ t('cv_tunnel.fields.full_name') }}</label>
    <input
      id="full-name"
      type="text"
      :placeholder="t('cv_tunnel.placeholders.full_name')"
      :aria-label="t('cv_tunnel.fields.full_name')"
    />

    <label for="phone">{{ t('cv_tunnel.fields.phone') }}</label>
    <input
      id="phone"
      type="tel"
      :placeholder="t('cv_tunnel.placeholders.phone')"
      :aria-label="t('cv_tunnel.fields.phone')"
    />

    <!-- Inline validation error using $t() in expression -->
    <p role="alert" class="text-rose-400">{{ t('cv_tunnel.validation.phone_invalid') }}</p>

    <button type="submit">{{ t('cv_tunnel.actions.continue') }}</button>
    <button type="button">{{ t('cv_tunnel.actions.cancel') }}</button>
  </form>

  <!-- Hero CTA buttons ────────────────────────────────────────────────── -->
  <div>
    <button>{{ t('hero.cta_cv') }}</button>
    <button>{{ t('hero.cta_contact') }}</button>
  </div>

  <!-- Project status badge ────────────────────────────────────────────── -->
  <!-- Pattern: use $t() with a dynamic key interpolated from data -->
  <!-- <span>{{ t(`projects.status.${project.status}`) }}</span> -->

  <!-- Footer rights with year interpolation ──────────────────────────── -->
  <p>{{ t('footer.rights', { year: new Date().getFullYear() }) }}</p>
</template>
