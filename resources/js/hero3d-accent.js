import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import volleyballModelUrl from '../models/volleyball.glb';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));
const lerp = (a, b, t) => a + (b - a) * t;
const smoothstep = (t) => t * t * (3 - 2 * t);

/**
 * Ambient 3D accent in the pinned editorial hero — a real volleyball that
 * starts big on the left, spins its way across to the right as you scroll
 * through the track, then fades out right before the 3D rally section
 * takes over. A drifting particle field and a slow-spinning halo ring sit
 * behind it for a fuller 3D backdrop with its own independent motion.
 * Falls back to a quiet idle spin if the editorial track isn't present or
 * under prefers-reduced-motion.
 */
export default function initHero3DAccent() {
  const canvas = document.querySelector('[data-hero3d-accent-canvas]');
  if (!canvas) return;

  const box = document.querySelector('[data-hero3d-accent-box]');
  const wrap = document.querySelector('[data-hero3d-accent-wrap]');
  const track = document.querySelector('[data-editorial-track]');

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Read the real design tokens instead of duplicating their hex values here.
  const rootStyles = getComputedStyle(document.documentElement);
  const tokenColor = (name, fallback) => new THREE.Color(rootStyles.getPropertyValue(name).trim() || fallback);
  const inkColor = tokenColor('--color-ink', '#0A1518');
  const mintColor = tokenColor('--color-green-mint', '#6FC8B5');

  const scene = new THREE.Scene();
  scene.fog = new THREE.Fog(inkColor, 3, 9.5);

  const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 20);
  camera.position.set(0, 0, 5.4);

  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

  const ball = new THREE.Group();
  ball.rotation.set(0.3, -0.4, 0.15);
  scene.add(ball);

  new GLTFLoader().load(
    volleyballModelUrl,
    (gltf) => {
      const model = gltf.scene;
      const box3 = new THREE.Box3().setFromObject(model);
      const sphere = new THREE.Sphere();
      box3.getBoundingSphere(sphere);

      const scale = sphere.radius > 0 ? 1.65 / sphere.radius : 1;
      model.scale.setScalar(scale);
      model.position.sub(sphere.center.clone().multiplyScalar(scale));

      ball.add(model);
      renderer.render(scene, camera);
    },
    undefined,
    (error) => console.error('skduo: failed to load volleyball model', error),
  );

  // Ambient depth: a loose sphere of drifting mint points behind the ball,
  // plus a faint halo ring — both spin independently of scroll so the
  // backdrop stays alive even while the ball itself sits still.
  const particleCount = 60;
  const particlePositions = new Float32Array(particleCount * 3);
  for (let i = 0; i < particleCount; i += 1) {
    const radius = 2.6 + Math.random() * 2.6;
    const theta = Math.random() * Math.PI * 2;
    const phi = Math.acos(Math.random() * 2 - 1);
    particlePositions[i * 3] = radius * Math.sin(phi) * Math.cos(theta);
    particlePositions[i * 3 + 1] = radius * Math.sin(phi) * Math.sin(theta) * 0.6;
    particlePositions[i * 3 + 2] = radius * Math.cos(phi) * 0.6 - 1.8;
  }
  const particleGeometry = new THREE.BufferGeometry();
  particleGeometry.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3));
  const particles = new THREE.Points(particleGeometry, new THREE.PointsMaterial({
    color: mintColor,
    size: 0.05,
    transparent: true,
    opacity: 0.55,
    sizeAttenuation: true,
  }));
  scene.add(particles);

  const halo = new THREE.Mesh(
    new THREE.RingGeometry(1.85, 1.95, 64),
    new THREE.MeshBasicMaterial({ color: mintColor, transparent: true, opacity: 0.22, side: THREE.DoubleSide }),
  );
  halo.rotation.x = Math.PI / 2.3;
  scene.add(halo);

  const key = new THREE.DirectionalLight(0xffffff, 1.6);
  key.position.set(-3, 4, 5);
  scene.add(key);
  scene.add(new THREE.AmbientLight(0x6fc8b5, 1.25));

  // Measure off the static outer box, not the wrap we transform below — its
  // rendered rect would shrink/grow with our own CSS scale and feed back
  // into a wrong renderer resolution.
  const measureEl = box || canvas;
  let viewportWidth = window.innerWidth;
  const resize = () => {
    const rect = measureEl.getBoundingClientRect();
    renderer.setSize(rect.width, rect.height, false);
    camera.aspect = rect.width / Math.max(1, rect.height);
    camera.updateProjectionMatrix();
    viewportWidth = window.innerWidth;
  };

  let progress = 0;
  let targetProgress = 0;
  let spinBoost = 0;
  let lastScrollY = window.scrollY;
  let rafId = null;

  const updateProgress = () => {
    if (!track) return;
    const rect = track.getBoundingClientRect();
    targetProgress = clamp(-rect.top / Math.max(1, rect.height - window.innerHeight));
  };

  const loop = () => {
    progress += (targetProgress - progress) * 0.08;

    // Travel: eases from big-on-the-left to settled-on-the-right across most
    // of the track, so it lands before the fade-out kicks in.
    const travelEased = smoothstep(clamp(progress / 0.8));
    // Shrink from an oversized start down to its normal size early on.
    const settleEased = smoothstep(clamp(progress / 0.3));
    // Fade out right as this pinned section hands off to the 3D rally track.
    const fadeOut = 1 - smoothstep(clamp((progress - 0.82) / 0.18));

    if (wrap && track) {
      const travel = viewportWidth * 0.36;
      const x = lerp(-travel, travel, travelEased);
      const scale = lerp(1.9, 1.15, settleEased);
      wrap.style.opacity = String(fadeOut);
      wrap.style.transform = `translate3d(${x}px, 0, 0) scale(${scale})`;
    }

    // Spins fastest while it's actively crossing the screen, easing toward a
    // slow idle once it has settled on the right.
    const travelSpeed = 1 - Math.abs(travelEased - 0.5) * 2;
    ball.rotation.y += 0.006 + spinBoost + travelSpeed * 0.05;
    ball.rotation.x += 0.003 + spinBoost * 0.55;
    spinBoost *= 0.92;

    // Backdrop keeps drifting on its own clock, independent of scroll, and
    // fades out together with the ball.
    particles.rotation.y += 0.0016;
    particles.rotation.x += 0.0006;
    particles.material.opacity = 0.55 * fadeOut;
    halo.rotation.z += 0.004;
    halo.material.opacity = 0.22 * fadeOut;

    renderer.render(scene, camera);
    rafId = requestAnimationFrame(loop);
  };

  let inViewport = false;
  const startLoop = () => { if (rafId === null) rafId = requestAnimationFrame(loop); };
  const stopLoop = () => {
    if (rafId !== null) cancelAnimationFrame(rafId);
    rafId = null;
  };

  const visibilityObserver = new IntersectionObserver(([entry]) => {
    inViewport = entry.isIntersecting;
    if (inViewport && document.visibilityState === 'visible' && !prefersReducedMotion) startLoop();
    else stopLoop();
  }, { threshold: 0 });
  visibilityObserver.observe(canvas);

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && inViewport && !prefersReducedMotion) startLoop();
    else stopLoop();
  });

  window.addEventListener('resize', resize);
  resize();

  if (prefersReducedMotion) {
    if (wrap) {
      wrap.style.opacity = '1';
      wrap.style.transform = 'none';
    }
    renderer.render(scene, camera);
    return;
  }

  window.addEventListener('scroll', () => {
    const delta = Math.abs(window.scrollY - lastScrollY);
    lastScrollY = window.scrollY;
    spinBoost = Math.min(0.05, spinBoost + delta * 0.0006);
    updateProgress();
    if (inViewport) startLoop();
  }, { passive: true });

  updateProgress();
}
