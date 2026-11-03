import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));
const lerp = (a, b, t) => a + (b - a) * t;
const smoothstep = (t) => t * t * (3 - 2 * t);

// Deterministic pseudo-random in [0, 1) per trophy index — varies each
// trophy's fall without any per-frame randomness (stays reversible on scroll-up).
const seeded = (i) => {
  const x = Math.sin(i * 12.9898 + 78.233) * 43758.5453;
  return x - Math.floor(x);
};

// Classic trophy-cup silhouette (radius, height) revolved around the Y axis —
// reads as an actual trophy instead of stacked primitives, and picks up real
// specular highlights + reflections from the room environment below.
const CUP_PROFILE = [
  [0.0, 0.0],
  [0.19, 0.0],
  [0.19, 0.035],
  [0.1, 0.075],
  [0.05, 0.15],
  [0.045, 0.27],
  [0.07, 0.315], // decorative knop between stem and bowl
  [0.055, 0.345],
  [0.095, 0.39],
  [0.16, 0.435],
  [0.22, 0.49],
  [0.255, 0.55],
  [0.265, 0.6], // widest point of the bowl
  [0.25, 0.65],
  [0.215, 0.69],
  [0.2, 0.715],
  [0.213, 0.73], // lip flares back out slightly at the rim
].map(([x, y]) => new THREE.Vector2(x, y));

const BOWL_WIDEST_Y = 0.6;

let sharedGeometry = null;
function getCupGeometry() {
  if (!sharedGeometry) sharedGeometry = new THREE.LatheGeometry(CUP_PROFILE, 40);
  return sharedGeometry;
}

/**
 * A trophy built from one lathe-revolved cup + a plinth + two handles.
 * Nested in a pivot group whose origin sits at the trophy's front-bottom edge,
 * so rotating the pivot tips it over that edge instead of through its own center.
 */
function createTrophy({ cupMaterial, plinthMaterial }) {
  const halfBase = 0.24;

  const pivot = new THREE.Group();
  const body = new THREE.Group();
  body.position.x = halfBase;
  pivot.add(body);

  const plinth = new THREE.Mesh(new THREE.CylinderGeometry(0.24, 0.26, 0.1, 20), plinthMaterial);
  plinth.position.y = 0.05;
  body.add(plinth);

  const cup = new THREE.Mesh(getCupGeometry(), cupMaterial);
  cup.position.y = 0.1;
  body.add(cup);

  const handleGeometry = new THREE.TorusGeometry(0.12, 0.016, 10, 24, Math.PI * 1.05);
  [-1, 1].forEach((side) => {
    const handle = new THREE.Mesh(handleGeometry, cupMaterial);
    handle.position.set(side * 0.26, 0.1 + BOWL_WIDEST_Y, 0);
    handle.rotation.z = Math.PI / 2;
    handle.rotation.y = side > 0 ? 0 : Math.PI;
    body.add(handle);
  });

  return pivot;
}

/**
 * Rotation, drop, and scatter for one trophy at local progress t (0–1).
 * 0–0.55: tips over its base edge. 0.55–1: falls to the floor with an
 * ease-in (gravity-like) drop while continuing to tumble, landing scattered
 * so a full shelf doesn't look like it fell in unison.
 */
function trophyPose(t, seed, shelfY) {
  const tipEase = smoothstep(clamp(t / 0.55));
  const fallEase = clamp((t - 0.55) / 0.45);
  const fallEaseIn = fallEase * fallEase;
  const direction = seed > 0.5 ? 1 : -1;

  return {
    spin: direction * (tipEase * 1.4 + fallEaseIn * (2.1 + seed * 1.6)),
    wobble: fallEaseIn * (seed - 0.5) * 1.1,
    y: shelfY - fallEaseIn * shelfY,
    scatterX: direction * fallEaseIn * (0.3 + seed * 0.55),
    scatterZ: fallEaseIn * (0.35 + seed * 0.7),
    landed: fallEase,
  };
}

export default function initTrophyShelf() {
  const canvas = document.querySelector('[data-trophy-canvas]');
  if (!canvas) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const rootStyles = getComputedStyle(document.documentElement);
  const tokenColor = (name, fallback) => {
    const value = rootStyles.getPropertyValue(name).trim();
    return new THREE.Color(value || fallback).getHex();
  };
  const mintColor = tokenColor('--color-green-mint', '#6FC8B5');

  const trophyCount = Math.max(1, Math.min(8, parseInt(canvas.dataset.trophyCount, 10) || 5));

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 30);
  const baseFov = 34;
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.5;

  // A generated room (not a loaded HDR) so the gold cups pick up real reflections
  // and specular falloff instead of the flat/dull look a metal material gets with
  // no environment to reflect — a few lines, no extra network asset.
  const pmremGenerator = new THREE.PMREMGenerator(renderer);
  scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
  pmremGenerator.dispose();

  const cupMaterial = new THREE.MeshStandardMaterial({
    color: 0xe8bd6e,
    metalness: 0.82,
    roughness: 0.3,
    envMapIntensity: 1.3,
  });
  const plinthMaterial = new THREE.MeshStandardMaterial({ color: 0x151009, roughness: 0.55, metalness: 0.1 });
  const shelfMaterial = new THREE.MeshStandardMaterial({ color: 0x2a1c12, roughness: 0.75, metalness: 0.05 });
  const shelfEdgeMaterial = new THREE.MeshStandardMaterial({ color: 0x4a3320, roughness: 0.6, metalness: 0.05 });

  const shelfY = 1.15;
  const shelfWidth = trophyCount > 1 ? Math.min(5.2, 1.05 * trophyCount) : 1.4;

  const shelf = new THREE.Mesh(new THREE.BoxGeometry(shelfWidth + 0.6, 0.12, 0.85), shelfMaterial);
  shelf.position.y = shelfY - 0.06;
  scene.add(shelf);

  const shelfEdge = new THREE.Mesh(new THREE.BoxGeometry(shelfWidth + 0.6, 0.02, 0.85), shelfEdgeMaterial);
  shelfEdge.position.y = shelfY;
  scene.add(shelfEdge);

  // Console-table legs, planted directly under the shelf ends at the shelf's own
  // depth (not set back like a wall bracket would be) — reads correctly as
  // furniture from an angled camera instead of an odd floating diagonal.
  [-1, 1].forEach((side) => {
    const leg = new THREE.Mesh(new THREE.BoxGeometry(0.14, shelfY - 0.06, 0.14), shelfMaterial);
    leg.position.set(side * (shelfWidth / 2 - 0.16), (shelfY - 0.06) / 2, 0);
    scene.add(leg);
  });

  const trophies = [];
  const slotSpacing = trophyCount > 1 ? shelfWidth / (trophyCount - 1) : 0;

  for (let i = 0; i < trophyCount; i += 1) {
    const pivot = createTrophy({ cupMaterial, plinthMaterial });
    const slotX = trophyCount > 1 ? -shelfWidth / 2 + slotSpacing * i : 0;
    pivot.position.set(slotX - 0.24, shelfY, 0);
    scene.add(pivot);

    const shadow = new THREE.Mesh(
      new THREE.CircleGeometry(0.3, 24),
      new THREE.MeshBasicMaterial({ color: 0x000000, transparent: true, opacity: 0.24 }),
    );
    shadow.rotation.x = -Math.PI / 2;
    shadow.position.set(slotX, 0.01, 0);
    scene.add(shadow);

    trophies.push({
      pivot,
      shadow,
      slotX,
      seed: seeded(i),
      current: 0,
      target: 0,
      hitPoint: new THREE.Vector3(slotX, shelfY + 0.4, 0),
    });
  }

  // Key + fill + a brand-mint rim light for a bit of edge separation from the dark hero backdrop.
  const key = new THREE.DirectionalLight(0xfff3d6, 3.2);
  key.position.set(-3, 6, 4.5);
  scene.add(key);

  const fill = new THREE.DirectionalLight(0xffe9c2, 0.9);
  fill.position.set(4, 2.5, -2);
  scene.add(fill);

  const rim = new THREE.DirectionalLight(mintColor, 2.1);
  rim.position.set(2, 3.5, -4);
  scene.add(rim);

  scene.add(new THREE.AmbientLight(0xffffff, 0.4));

  // A near-frontal, gently elevated view — the previous strong side offset made the
  // console legs (and anything else off-center) rake diagonally under perspective.
  camera.position.set(0.1, 1.6, 4.9);
  camera.lookAt(0, 0.95, 0);

  const resize = () => {
    const rect = canvas.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return;
    renderer.setSize(rect.width, rect.height, false);
    const aspect = rect.width / rect.height;
    camera.aspect = aspect;
    // A hero section is often very wide and comparatively short — widen the
    // vertical FOV on extreme aspects so the shelf doesn't zoom in absurdly.
    camera.fov = aspect > 2.1 ? Math.min(58, baseFov * (aspect / 2.1)) : baseFov;
    camera.updateProjectionMatrix();
  };

  const renderFrame = () => {
    trophies.forEach((trophy) => {
      const pose = trophyPose(trophy.current, trophy.seed, shelfY);

      trophy.pivot.rotation.z = -pose.spin;
      trophy.pivot.rotation.x = pose.wobble;
      trophy.pivot.position.set(trophy.slotX - 0.24 + pose.scatterX, pose.y, pose.scatterZ);

      trophy.shadow.position.set(trophy.slotX + pose.scatterX, 0.01, pose.scatterZ);
      trophy.shadow.material.opacity = lerp(0.06, 0.26, pose.landed);
      trophy.shadow.scale.setScalar(lerp(0.7, 1.15, pose.landed));
    });

    renderer.render(scene, camera);
  };

  let rafId = null;

  const render = () => {
    let settled = true;
    trophies.forEach((trophy) => {
      trophy.current += (trophy.target - trophy.current) * 0.08;
      if (Math.abs(trophy.target - trophy.current) > 0.0008) settled = false;
    });
    renderFrame();

    if (settled) {
      rafId = null;
      return;
    }
    rafId = requestAnimationFrame(render);
  };

  const startRender = () => {
    if (rafId === null) rafId = requestAnimationFrame(render);
  };
  const stopRender = () => {
    if (rafId !== null) cancelAnimationFrame(rafId);
    rafId = null;
  };

  // Only spend GPU time while the shelf is actually on screen, and pause when the tab is hidden.
  let inViewport = false;
  const visibilityObserver = new IntersectionObserver(([entry]) => {
    inViewport = entry.isIntersecting;
    if (inViewport && document.visibilityState === 'visible') startRender();
    else stopRender();
  }, { threshold: 0 });
  visibilityObserver.observe(canvas);

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && inViewport) startRender();
    else stopRender();
  });

  window.addEventListener('resize', resize);
  resize();
  renderFrame();

  if (prefersReducedMotion) {
    // Skip the pointer toy entirely — a hover-triggered spin-and-fall is exactly
    // the kind of motion prefers-reduced-motion asks sites to avoid.
    return;
  }

  // Push a trophy off the shelf by moving the pointer near it — no click needed,
  // just passing close enough. Once knocked over it stays fallen (no reset on
  // pointer-leave); reloading the page is the only way to set the shelf again.
  const hitRadiusPx = 62;
  const screenPoint = new THREE.Vector3();

  canvas.addEventListener('pointermove', (event) => {
    const rect = canvas.getBoundingClientRect();
    const pointerX = event.clientX - rect.left;
    const pointerY = event.clientY - rect.top;
    let hit = false;

    trophies.forEach((trophy) => {
      if (trophy.target >= 1) return;

      screenPoint.copy(trophy.hitPoint).project(camera);
      const px = (screenPoint.x * 0.5 + 0.5) * rect.width;
      const py = (-screenPoint.y * 0.5 + 0.5) * rect.height;
      const distance = Math.hypot(px - pointerX, py - pointerY);

      if (distance < hitRadiusPx) {
        trophy.target = 1;
        hit = true;
      }
    });

    if (hit && inViewport) startRender();
  }, { passive: true });
}
