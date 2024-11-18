// Create the scene, camera, and renderer
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(window.devicePixelRatio);
document.getElementById('canvas-container').appendChild(renderer.domElement);

// Add ambient and point lighting
const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
scene.add(ambientLight);

const pointLight = new THREE.PointLight(0xffffff, 1.2);
pointLight.position.set(5, 10, 10);
scene.add(pointLight);

// Create a grid of chairs
const chairGeometry = new THREE.BoxGeometry(1, 0.5, 1);
const chairMaterial = new THREE.MeshStandardMaterial({ color: 0x007bff });
const chairs = [];

const gridSize = 5; // Grid size (5x5)
const chairSpacing = 2; // Distance between chairs

for (let i = 0; i < gridSize; i++) {
  for (let j = 0; j < gridSize; j++) {
    const chair = new THREE.Mesh(chairGeometry, chairMaterial);
    chair.position.set(
      i * chairSpacing - (gridSize / 2) * chairSpacing, 
      10, 
      j * chairSpacing - (gridSize / 2) * chairSpacing
    ); // Start high
    chairs.push(chair);
    scene.add(chair);
  }
}

// Add student spheres to each chair
const studentGeometry = new THREE.SphereGeometry(0.3, 32, 32);
const studentMaterial = new THREE.MeshStandardMaterial({ color: 0xff5722 });
const students = [];

chairs.forEach((chair) => {
  const student = new THREE.Mesh(studentGeometry, studentMaterial);
  student.position.set(chair.position.x, chair.position.y + 1, chair.position.z);
  students.push(student);
  scene.add(student);
});

// Position the camera
camera.position.set(0, 10, 20); // Move back to see the grid
camera.lookAt(0, 0, 0); // Focus on the grid center

// Animate chairs dropping and students sitting
chairs.forEach((chair, index) => {
  gsap.to(chair.position, {
    y: 0, // Drop to ground level
    duration: 1,
    delay: index * 0.1, // Staggered animation
    ease: "bounce.out",
    onComplete: () => {
      gsap.to(students[index].position, {
        y: 0.8, // Sit slightly above the chair
        duration: 0.5,
        ease: "power1.out",
      });
    },
  });
});

// Animate title to drop below and above the chairs
gsap.to("#title", {
  y: 3, // Move title below and just above the chairs
  opacity: 1, // Fade in
  duration: 2,
  ease: "power2.out",
  delay: 3, // Delay until after chair and student animation
});

// Render loop
function animate() {
  requestAnimationFrame(animate);
  renderer.render(scene, camera);
}

animate();

// Make the canvas responsive
window.addEventListener('resize', () => {
  renderer.setSize(window.innerWidth, window.innerHeight);
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
});
