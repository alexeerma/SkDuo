import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import volleyballModelUrl from '../models/volleyball.glb';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));
const lerp = (a, b, t) => a + (b - a) * t;
const smoothstep = (t) => t * t * (3 - 2 * t);

export default function initRally3D() {
  const canvas = document.querySelector('[data-rally-canvas]');
  const track = document.querySelector('[data-rally-track]');
  if (!canvas || !track) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Read the real design tokens instead of duplicating their hex values here,
  // so the canvas can't drift from the CSS palette.
  const rootStyles = getComputedStyle(document.documentElement);
  const tokenColor = (name, fallback) => {
    const value = rootStyles.getPropertyValue(name).trim();
    return new THREE.Color(value || fallback).getHex();
  };
  const inkColor = tokenColor('--color-ink', '#0A1518');
  const ink2Color = tokenColor('--color-ink-2', '#15282C');

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(inkColor);
  scene.fog = new THREE.Fog(inkColor, 18, 52);

  const camera = new THREE.PerspectiveCamera(48, 1, 0.1, 120);
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: false });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

  const court = new THREE.Group();
  scene.add(court);

  const floor = new THREE.Mesh(
    new THREE.PlaneGeometry(24, 13),
    new THREE.MeshBasicMaterial({ color: ink2Color }),
  );
  floor.rotation.x = -Math.PI / 2;
  court.add(floor);

  const addLine = (points, color = 0xf7faef, opacity = 0.55) => {
    const geometry = new THREE.BufferGeometry().setFromPoints(
      points.map(([x, z]) => new THREE.Vector3(x, 0.02, z)),
    );
    const material = new THREE.LineBasicMaterial({ color, transparent: true, opacity });
    const line = new THREE.Line(geometry, material);
    court.add(line);
    return line;
  };

  addLine([[-9, -4.5], [9, -4.5], [9, 4.5], [-9, 4.5], [-9, -4.5]], 0xf8f5ee, 0.75);
  addLine([[0, -4.5], [0, 4.5]], 0xf8f5ee, 0.8);
  addLine([[-3, -4.5], [-3, 4.5]], 0x6fc8b5, 0.32);
  addLine([[3, -4.5], [3, 4.5]], 0x6fc8b5, 0.32);

  for (let z = -4.5; z <= 4.51; z += 0.32) {
    const geometry = new THREE.BufferGeometry().setFromPoints([
      new THREE.Vector3(0, 0.05, z),
      new THREE.Vector3(0, 2.43, z),
    ]);
    court.add(new THREE.Line(
      geometry,
      new THREE.LineBasicMaterial({ color: 0xf8f5ee, transparent: true, opacity: 0.16 }),
    ));
  }

  for (let y = 0.22; y <= 2.43; y += 0.24) {
    const geometry = new THREE.BufferGeometry().setFromPoints([
      new THREE.Vector3(0, y, -4.5),
      new THREE.Vector3(0, y, 4.5),
    ]);
    court.add(new THREE.Line(
      geometry,
      new THREE.LineBasicMaterial({ color: 0xf8f5ee, transparent: true, opacity: 0.13 }),
    ));
  }

  const tape = new THREE.Mesh(
    new THREE.BoxGeometry(0.05, 0.08, 9.15),
    new THREE.MeshBasicMaterial({ color: 0xf8f5ee }),
  );
  tape.position.y = 2.43;
  court.add(tape);

  [-4.65, 4.65].forEach((z) => {
    const pole = new THREE.Mesh(
      new THREE.CylinderGeometry(0.05, 0.05, 2.8, 16),
      new THREE.MeshBasicMaterial({ color: 0xf8f5ee, transparent: true, opacity: 0.45 }),
    );
    pole.position.set(0, 1.4, z);
    court.add(pole);
  });

  // Sponsor boards flanking the court — texture is drawn once each logo has
  // loaded (repeated a few times across the board, like a real ad board),
  // then mapped onto a pair of boards facing the court.
  const addSponsorBoardPair = (imageUrl, { boardWidth, boardHeight, axis, offset }) => {
    if (!imageUrl) return;

    const logoImage = new Image();
    logoImage.onload = () => {
      const boardAspect = boardWidth / boardHeight;
      const boardCanvas = document.createElement('canvas');
      boardCanvas.width = 2048;
      boardCanvas.height = Math.round(boardCanvas.width / boardAspect);
      const ctx = boardCanvas.getContext('2d');

      ctx.fillStyle = '#F8F5EE';
      ctx.fillRect(0, 0, boardCanvas.width, boardCanvas.height);

      const logoAspect = (logoImage.naturalWidth || 1) / (logoImage.naturalHeight || 1);
      const logoHeight = boardCanvas.height * 0.6;
      const logoWidth = logoHeight * logoAspect;
      const gap = logoWidth * 0.55;
      const unit = logoWidth + gap;
      const repeats = Math.max(1, Math.round(boardCanvas.width / unit));
      const totalWidth = repeats * unit - gap;
      let x = (boardCanvas.width - totalWidth) / 2;
      const y = (boardCanvas.height - logoHeight) / 2;

      for (let i = 0; i < repeats; i += 1) {
        ctx.drawImage(logoImage, x, y, logoWidth, logoHeight);
        x += unit;
      }

      const texture = new THREE.CanvasTexture(boardCanvas);
      texture.colorSpace = THREE.SRGBColorSpace;
      texture.anisotropy = renderer.capabilities.getMaxAnisotropy();

      const boardGeometry = new THREE.PlaneGeometry(boardWidth, boardHeight);
      const boardMaterial = new THREE.MeshBasicMaterial({ map: texture });

      const boardA = new THREE.Mesh(boardGeometry, boardMaterial);
      const boardB = new THREE.Mesh(boardGeometry, boardMaterial);

      if (axis === 'x') {
        // Baseline boards: stand at both ends, facing along X toward center court.
        boardA.position.set(-offset, boardHeight / 2, 0);
        boardA.rotation.y = Math.PI / 2;
        boardB.position.set(offset, boardHeight / 2, 0);
        boardB.rotation.y = -Math.PI / 2;
      } else {
        // Sideline boards: stand along both long edges, facing along Z toward center court.
        boardA.position.set(0, boardHeight / 2, -offset);
        boardB.position.set(0, boardHeight / 2, offset);
        boardB.rotation.y = Math.PI;
      }

      court.add(boardA, boardB);

      // Boards load asynchronously — repaint immediately so they don't wait
      // for the next scroll tick (or ever appear, once settled/reduced-motion).
      if (typeof renderFrame === 'function') renderFrame();
    };
    logoImage.src = imageUrl;
  };

  addSponsorBoardPair(track.dataset.rallySponsorLogo, {
    boardWidth: 14, boardHeight: 1, axis: 'z', offset: 5.3,
  });
  addSponsorBoardPair(track.dataset.rallyCityLogo, {
    boardWidth: 8, boardHeight: 1.1, axis: 'x', offset: 9.6,
  });

  // Real Mikasa V200W model. Loads async, so `ball` is a group added to the
  // scene immediately — position/rotation updates below are safe from frame
  // one, the mesh itself just fades in once the GLTF resolves.
  const ball = new THREE.Group();
  scene.add(ball);

  new GLTFLoader().load(
    volleyballModelUrl,
    (gltf) => {
      const model = gltf.scene;

      const box = new THREE.Box3().setFromObject(model);
      const sphere = new THREE.Sphere();
      box.getBoundingSphere(sphere);

      const targetRadius = 0.24;
      const scale = sphere.radius > 0 ? targetRadius / sphere.radius : 1;
      model.scale.setScalar(scale);
      model.position.sub(sphere.center.clone().multiplyScalar(scale));

      ball.add(model);
      if (typeof renderFrame === 'function') renderFrame();
    },
    undefined,
    (error) => console.error('skduo: failed to load volleyball model', error),
  );

  const shadow = new THREE.Mesh(
    new THREE.CircleGeometry(0.34, 32),
    new THREE.MeshBasicMaterial({ color: 0x000000, transparent: true, opacity: 0.35 }),
  );
  shadow.rotation.x = -Math.PI / 2;
  scene.add(shadow);

  const path = [
    {
      label: 'Serv',
      desc: 'Mäng algab survega tagajoone tagant.',
      pos: new THREE.Vector3(-8.2, 2.65, -3.15),
      floor: new THREE.Vector3(-8.2, 0.04, -3.15),
      arc: 1.2,
    },
    {
      label: 'Vastuvõtt',
      desc: 'Esimene puude hoiab ralli elus.',
      pos: new THREE.Vector3(6.4, 1.0, 2.85),
      floor: new THREE.Vector3(6.4, 0.04, 2.85),
      arc: 0.75,
    },
    {
      label: 'Tõste',
      desc: 'Sidemängija loob rünnakule aja ja nurga.',
      pos: new THREE.Vector3(0.65, 3.25, -1.3),
      floor: new THREE.Vector3(0.65, 0.04, -1.3),
      arc: 1.0,
    },
    {
      label: 'Rünnak',
      desc: 'Löök tuleb nurgast, otse võrgu kõrvalt.',
      pos: new THREE.Vector3(0.5, 2.85, -3.75),
      floor: new THREE.Vector3(0.5, 0.04, -3.75),
      arc: 0.55,
    },
    {
      label: 'Kaitse',
      desc: 'Tagaliin loeb löögi ja toob palli uuesti mängu.',
      pos: new THREE.Vector3(-5.2, 0.9, 1.25),
      floor: new THREE.Vector3(-5.2, 0.04, 1.25),
      arc: 1.15,
    },
  ];

  const playerMoves = [
    {
      start: new THREE.Vector3(-8.9, 0.04, -3.15),
      end: path[0].floor,
      from: 0,
      to: 0.18,
    },
    {
      start: new THREE.Vector3(7.45, 0.04, 3.55),
      end: path[1].floor,
      from: 0.18,
      to: 0.35,
    },
    {
      start: new THREE.Vector3(7.55, 0.04, -3.75),
      end: path[2].floor,
      from: 0.35,
      to: 0.58,
    },
    {
      start: new THREE.Vector3(2.7, 0.04, -4.15),
      end: path[3].floor,
      from: 0.58,
      to: 0.76,
    },
    {
      start: new THREE.Vector3(-6.2, 0.04, 2.35),
      end: path[4].floor,
      from: 0.76,
      to: 0.96,
    },
  ];

  const markerGroup = new THREE.Group();
  scene.add(markerGroup);

  path.forEach((point, index) => {
    const ring = new THREE.Mesh(
      new THREE.RingGeometry(0.34, 0.42, 48),
      new THREE.MeshBasicMaterial({
        color: index === 0 ? 0x6fc8b5 : 0xf8f5ee,
        transparent: true,
        opacity: index === 0 ? 0.72 : 0.26,
        side: THREE.DoubleSide,
      }),
    );
    ring.rotation.x = -Math.PI / 2;
    ring.position.copy(playerMoves[index].start);
    ring.userData.kind = 'ring';
    ring.userData.index = index;
    markerGroup.add(ring);

    const beacon = new THREE.Line(
      new THREE.BufferGeometry().setFromPoints([playerMoves[index].start, point.pos]),
      new THREE.LineBasicMaterial({
        color: 0x6fc8b5,
        transparent: true,
        opacity: 0.18,
      }),
    );
    beacon.userData.kind = 'beacon';
    beacon.userData.index = index;
    markerGroup.add(beacon);
  });

  const moveAt = (move, t) => {
    const amount = clamp((t - move.from) / Math.max(0.001, move.to - move.from));
    const eased = smoothstep(amount);
    return new THREE.Vector3(
      lerp(move.start.x, move.end.x, eased),
      lerp(move.start.y, move.end.y, eased),
      lerp(move.start.z, move.end.z, eased),
    );
  };

  const updatePlayers = (t) => {
    markerGroup.children.forEach((marker) => {
      const move = playerMoves[marker.userData.index];
      if (!move) return;

      const floorPosition = moveAt(move, t);
      if (marker.userData.kind === 'ring') {
        marker.position.copy(floorPosition);
      }

      if (marker.userData.kind === 'beacon') {
        marker.geometry.setFromPoints([floorPosition, path[marker.userData.index].pos]);
      }
    });
  };

  const sampleTrajectory = () => {
    const samples = [];
    for (let i = 0; i <= 160; i += 1) {
      samples.push(pointAt(i / 160).position);
    }
    return samples;
  };

  const hudNum = document.querySelector('[data-rally-num]');
  const hudLabel = document.querySelector('[data-rally-label]');
  const hudDesc = document.querySelector('[data-rally-desc]');
  const pips = [...document.querySelectorAll('[data-rally-pip]')];

  const light = new THREE.DirectionalLight(0xffffff, 1.4);
  light.position.set(-4, 8, 5);
  scene.add(light);
  scene.add(new THREE.AmbientLight(0x6fc8b5, 1.15));

  let targetProgress = 0;
  let progress = 0;
  let activeIndex = -1;

  const resize = () => {
    const rect = canvas.getBoundingClientRect();
    renderer.setSize(rect.width, rect.height, false);
    camera.aspect = rect.width / rect.height;
    camera.updateProjectionMatrix();
  };

  const updateProgress = () => {
    const rect = track.getBoundingClientRect();
    targetProgress = clamp(-rect.top / Math.max(1, rect.height - window.innerHeight));
  };

  const updateHud = (index) => {
    if (index === activeIndex && hudLabel?.textContent === path[index].label) return;
    activeIndex = index;

    if (hudNum) hudNum.textContent = `${String(index + 1).padStart(2, '0')} / 05`;
    if (hudLabel) hudLabel.textContent = path[index].label;
    if (hudDesc) hudDesc.textContent = path[index].desc;

    pips.forEach((pip, i) => {
      pip.style.background = i === index ? '#6FC8B5' : 'rgba(248,245,238,.25)';
      pip.style.opacity = i === index ? '1' : '.75';
    });

    markerGroup.children.forEach((marker) => {
      const selected = marker.userData.index === index;
      if (marker.userData.kind === 'ring') {
        marker.material.color.set(selected ? 0x6fc8b5 : 0xf8f5ee);
        marker.material.opacity = selected ? 0.78 : 0.24;
        marker.scale.setScalar(selected ? 1.22 : 1);
      }
      if (marker.userData.kind === 'beacon') {
        marker.material.opacity = selected ? 0.48 : 0.16;
      }
    });
  };

  const pointAt = (t) => {
    const snap = 0.055;
    const scaled = t * (path.length - 1);
    const nearest = Math.round(scaled);

    if (Math.abs(scaled - nearest) < snap && nearest >= 0 && nearest < path.length) {
      return {
        index: nearest,
        position: path[nearest].pos.clone(),
      };
    }

    const i = Math.min(path.length - 2, Math.floor(scaled));
    const local = clamp(scaled - i);
    const a = path[i].pos;
    const b = path[i + 1].pos;
    const eased = smoothstep(local);
    const segmentArc = Math.max(path[i].arc || 0, path[i + 1].arc || 0);

    return {
      index: clamp(Math.round(scaled), 0, path.length - 1),
      position: new THREE.Vector3(
        lerp(a.x, b.x, eased),
        lerp(a.y, b.y, eased) + Math.sin(local * Math.PI) * segmentArc,
        lerp(a.z, b.z, eased),
      ),
    };
  };

  const trajectory = new THREE.Line(
    new THREE.BufferGeometry().setFromPoints(sampleTrajectory()),
    new THREE.LineBasicMaterial({ color: 0x6fc8b5, transparent: true, opacity: 0.34 }),
  );
  scene.add(trajectory);

  let rafId = null;

  const renderFrame = () => {
    const { index, position } = pointAt(progress);
    ball.position.copy(position);

    shadow.position.set(position.x, 0.03, position.z);
    shadow.scale.setScalar(clamp(1.15 - position.y * 0.22, 0.28, 0.9));

    camera.position.set(lerp(-10, -4, progress), lerp(7.2, 5.7, progress), lerp(9.5, 7.5, progress));
    camera.lookAt(0, 1.1, 0);

    updatePlayers(progress);
    updateHud(clamp(index, 0, path.length - 1));
    renderer.render(scene, camera);
  };

  const render = () => {
    const previous = progress;
    progress += (targetProgress - progress) * 0.075;
    ball.rotation.x += 0.025;
    ball.rotation.z += 0.018;
    renderFrame();

    // Stop once the eased value has settled — a static frame doesn't need a
    // running rAF loop; updateProgress() below wakes it back up on scroll.
    if (Math.abs(targetProgress - progress) < 0.0005 && Math.abs(progress - previous) < 0.0005) {
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

  // Only spend GPU time while the rally section is actually on screen, and
  // pause outright when the tab is hidden — the loop would otherwise run
  // for the full page lifetime regardless of scroll position or focus.
  let inViewport = false;
  const visibilityObserver = new IntersectionObserver(([entry]) => {
    inViewport = entry.isIntersecting;
    if (inViewport && document.visibilityState === 'visible') startRender();
    else stopRender();
  }, { threshold: 0 });
  visibilityObserver.observe(track);

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && inViewport) startRender();
    else stopRender();
  });

  window.addEventListener('resize', resize);
  resize();

  if (prefersReducedMotion) {
    // No scroll-scrubbed animation: land on the final frame and leave it static.
    progress = 1;
    targetProgress = 1;
    renderFrame();
    return;
  }

  window.addEventListener('scroll', () => {
    updateProgress();
    if (inViewport) startRender();
  }, { passive: true });
  updateProgress();
}
