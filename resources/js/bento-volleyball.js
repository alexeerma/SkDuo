import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import volleyballModelUrl from '../models/volleyball.glb';

/**
 * Real volleyball model spinning in the corner of a blog bento card, in
 * place of a flat icon. Each card's WebGL context is created lazily on
 * first hover/focus and then just paused (not torn down) on mouse-out —
 * forcing context loss and immediately recreating it on the next hover
 * makes browsers refuse the new context and render a broken-image icon
 * instead. With at most a handful of bento cards per page, leaving their
 * contexts idle costs nothing and stays well under the browser's limit.
 * The GLTF itself loads once and is cloned per card from a cached promise.
 */
export default function initBentoVolleyball() {
  const cards = [...document.querySelectorAll('[data-bento-card]')];
  if (!cards.length) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const noHover = window.matchMedia('(hover: none)').matches;
  if (prefersReducedMotion || noHover) return;

  let modelPromise = null;
  const loadModel = () => {
    if (!modelPromise) {
      modelPromise = new Promise((resolve, reject) => {
        new GLTFLoader().load(volleyballModelUrl, (gltf) => resolve(gltf.scene), undefined, reject);
      });
    }
    return modelPromise;
  };

  cards.forEach((card) => {
    const canvas = card.querySelector('[data-bento-volleyball-canvas]');
    if (!canvas) return;

    let renderer = null;
    let scene = null;
    let camera = null;
    let ball = null;
    let rafId = null;
    let active = false;
    let requestId = 0;

    const resize = () => {
      if (!renderer) return;
      const size = Math.max(1, canvas.getBoundingClientRect().width);
      renderer.setSize(size, size, false);
      camera.aspect = 1;
      camera.updateProjectionMatrix();
    };

    const loop = () => {
      if (!active || !renderer) return;
      ball.rotation.y += 0.022;
      ball.rotation.x += 0.013;
      renderer.render(scene, camera);
      rafId = requestAnimationFrame(loop);
    };

    const start = () => {
      active = true;
      if (renderer) return;

      scene = new THREE.Scene();
      camera = new THREE.PerspectiveCamera(38, 1, 0.1, 10);
      camera.position.set(0, 0, 3.1);

      renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

      ball = new THREE.Group();
      ball.rotation.set(0.4, -0.5, 0.1);
      scene.add(ball);

      const key = new THREE.DirectionalLight(0xffffff, 1.7);
      key.position.set(-2, 3, 4);
      scene.add(key);
      scene.add(new THREE.AmbientLight(0x6fc8b5, 1.2));

      const thisRequest = (requestId += 1);
      loadModel().then((model) => {
        // A hover that ended before the (cached) model resolved shouldn't
        // populate a scene nobody is looking at anymore.
        if (thisRequest !== requestId || !scene) return;

        const clone = model.clone(true);
        const box = new THREE.Box3().setFromObject(clone);
        const sphere = new THREE.Sphere();
        box.getBoundingSphere(sphere);
        const scale = sphere.radius > 0 ? 1.05 / sphere.radius : 1;
        clone.scale.setScalar(scale);
        clone.position.sub(sphere.center.clone().multiplyScalar(scale));

        ball.add(clone);
      }).catch((error) => console.error('skduo: failed to load volleyball model', error));

      resize();
      loop();
    };

    const stop = () => {
      active = false;
      requestId += 1;
      if (rafId !== null) cancelAnimationFrame(rafId);
      rafId = null;
    };

    card.addEventListener('pointerenter', start);
    card.addEventListener('pointerleave', stop);
    card.addEventListener('focusin', start);
    card.addEventListener('focusout', stop);

    window.addEventListener('resize', () => {
      if (active) resize();
    });
  });
}
