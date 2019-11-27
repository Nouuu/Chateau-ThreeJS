<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Cours WebGL</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <style>
        body {
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>
<script type="module">
    import * as THREE from './three.module.js';
    import {OrbitControls} from './OrbitControls.js';

    Math.radians = function (degrees) {
        return degrees * Math.PI / 180;
    };
    var camera, scene, renderer, controls;
    var terrain, mesh, mesh2, mesh3, mesh4, mesh5, mesh6;
    var group = new THREE.Group();
    init();
    animate();

    function init() {

        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(70, window.innerWidth / window.innerHeight, 1, 3000);
        camera.position.z = 300;
        camera.position.y = 180;
        //camera.position.x = -100;
        camera.rotation.x -= 0.5;
        var geometry;

        /**
         * Textures matériel
         */
        var textMurPath = './tex/stone wall 7.png';
        var textMurStonePath = './tex/stone wall 10.png';
        var textMurStone2Path = './tex/stone wall 4.png';
        var textRoofPath = './tex/roof.jpg';
        var textRoofWoodPath = './tex/wood floor 2.png';
        var textWindowPath = './tex/window.jpg';
        var textDoorPath = './tex/door.jpg';
        var textWoodDoorPath = './tex/wood_door_01.png';

        var textGrass = new THREE.TextureLoader().load('./tex/grass1.png');
        var textPorte = new THREE.TextureLoader().load('./tex/door.jpg');
        var textGate = new THREE.TextureLoader().load(textMurPath);
        var textToit = new THREE.TextureLoader().load('./tex/roof.jpg');
        var textSky = new THREE.TextureLoader().load('./tex/sky.jpg');

        var materialGrass = new THREE.MeshBasicMaterial({map: textGrass});
        var materialPorte = new THREE.MeshBasicMaterial({map: textPorte});
        var materialGate = new THREE.MeshBasicMaterial({map: textGate});
        var materialToit = new THREE.MeshBasicMaterial({map: textToit});

        /**
         * Objets
         */
        var Gate = {
            Width: 40,
            Height: 50,
            Depth: 40,
            RoofRadiusTop: 0,
            RoofRadiusBottom: 28,
            RoofHeight: 20,
            RoofRadialSegment: 4,
            DoorWidth: 40,
            DoorHeight: 40
        };
        var FrontMuraille = {
            Width: 150,
            Height: 40,
            Depth: 20,
        };
        var Muraille = {
            Width: FrontMuraille.Width * 2 + Gate.Width,
            Height: 40,
            Depth: 20,
        };
        var Rempart = {
            Width: 8,
            Height: 10,
            Depth: 2,
        };
        var Tour = {
            Radius: 20,
            Height: 70,
            RadialSegment: 32,
            RoofRadiusTop: 0,
            RoofRadiusBottom: 25,
            RoofHeight: 30,
            RoofRadialSegment: 32,
            WindowWidth: 10,
            WindowHeigth: 10,
            WindowDepth: 3,
        };
        var Donjon = {
            Radius: 40,
            Height: 130,
            RadialSegment: 32,
            RoofRadiusTop: 0,
            RoofRadiusBottom: 50,
            RoofHeight: 70,
            RoofRadialSegment: 32,
            WindowWidth: 20,
            WindowHeigth: 20,
            WindowDepth: 3,
        };
        var House = {
            Width: 70,
            Height: 50,
            Depth: 70,
            RoofRadiusTop: 0,
            RoofRadiusBottom: 55,
            RoofHeight: 50,
            RoofRadialSegment: 4,
            DoorWidth: 30,
            DoorHeight: 30,
            WindowWidth: 15,
            WindowHeight: 10,
            WindowDepth: 3,
        };
        var House2 = {
            Width: 60,
            Height: 40,
            Depth: 40,
            RoofRadiusTop: 0,
            RoofRadiusBottom: 45,
            RoofHeight: 30,
            RoofRadialSegment: 4,
            DoorWidth: 30,
            DoorHeight: 30,
            WindowWidth: 10,
            WindowHeight: 10,
            WindowDepth: 3,
        };
        /**
         * Répétition des textures
         */
        textGate.wrapS = THREE.RepeatWrapping;
        textGate.wrapT = THREE.RepeatWrapping;
        textGate.repeat.set(Gate.Width / 20, Gate.Height / 20);

        textGrass.wrapS = THREE.RepeatWrapping;
        textGrass.wrapT = THREE.RepeatWrapping;
        textGrass.repeat.set(100, 100);
        /**
         * Ajout du terrain
         */
        geometry = new THREE.CircleGeometry(1000, 64);
        terrain = new THREE.Mesh(geometry, materialGrass);
        terrain.rotateX(Math.radians(-90));
        scene.add(terrain);


        /**
         * Muraille avant
         */
        var frontMuraille = createMuraille(FrontMuraille, Rempart, textMurPath);
        /**
         * Muraille longue
         */
        var muraille = createMuraille(Muraille, Rempart, textMurPath);
        /**
         * Tour
         */
        var tour = createTower(Tour, textMurPath, textRoofPath, textWindowPath);
        /**
         * Donjon
         */
        var donjon = createTower(Donjon, textMurPath, textRoofPath, textWindowPath);

        var house = createHouse(House, textMurStonePath, textRoofWoodPath, textWindowPath, textWoodDoorPath);
        var house2 = createHouse(House2, textMurStone2Path, textRoofWoodPath, textWindowPath, textWoodDoorPath);
        /**
         * Entrée + porte
         */
        var entree = new THREE.Group();
        mesh = new THREE.Mesh(new THREE.BoxGeometry(Gate.Width, Gate.Height, Gate.Depth), materialGate);
        mesh.position.set(0, Gate.Height / 2, 0);

        geometry = new THREE.PlaneGeometry(Gate.DoorWidth, Gate.DoorHeight);
        mesh2 = new THREE.Mesh(geometry, materialPorte);
        mesh2.position.set(0, Gate.DoorHeight / 2, Gate.Depth / 2 + 0.1);

        mesh3 = new THREE.Mesh(new THREE.CylinderBufferGeometry(
            Gate.RoofRadiusTop,
            Gate.RoofRadiusBottom,
            Gate.RoofHeight,
            Gate.RoofRadialSegment), materialToit);
        mesh3.position.y = Gate.Height + Gate.RoofHeight / 2;
        mesh3.rotateY(Math.radians(45));

        entree.add(mesh.clone());
        entree.add(mesh2.clone());
        entree.add(mesh3.clone());
        /**
         * Affichage
         */
        house.position.set(100, 0, 0);
        instanceToScene(house);
        house2.position.set(-100, 0, 0);
        instanceToScene(house2);
        donjon.position.set(0, 0, 0);
        instanceToScene(donjon);
        tour.position.set(-(Tour.Radius + Muraille.Width / 2 - 5), 0, Tour.Radius + Muraille.Width / 2 - 5);
        instanceToScene(tour);
        tour.position.set(Tour.Radius + Muraille.Width / 2 - 5, 0, Tour.Radius + Muraille.Width / 2 - 5);
        instanceToScene(tour);
        tour.position.set(-(Tour.Radius + Muraille.Width / 2 - 5), 0, -(Tour.Radius + Muraille.Width / 2 - 5));
        instanceToScene(tour);
        tour.position.set(Tour.Radius + Muraille.Width / 2 - 5, 0, -(Tour.Radius + Muraille.Width / 2 - 5));
        instanceToScene(tour);
        frontMuraille.position.set(-(FrontMuraille.Width + Gate.Width) / 2, 0, (Muraille.Depth + Muraille.Width + Tour.Radius) / 2);
        instanceToScene(frontMuraille);
        frontMuraille.position.set((FrontMuraille.Width + Gate.Width) / 2, 0, (Muraille.Depth + Muraille.Width + Tour.Radius) / 2);
        instanceToScene(frontMuraille);
        muraille.position.set(0, 0, -(Muraille.Depth + Muraille.Width + Tour.Radius) / 2);
        instanceToScene(muraille);
        muraille.position.set(-(Muraille.Depth + Muraille.Width + Tour.Radius) / 2, 0, 0);
        muraille.rotation.y = Math.radians(90);
        instanceToScene(muraille);
        muraille.position.set((Muraille.Depth + Muraille.Width + Tour.Radius) / 2, 0, 0);
        instanceToScene(muraille);
        entree.position.set(0, 0, frontMuraille.position.z);
        instanceToScene(entree);
        group.add(terrain);

        /**
         * Options de rendu
         */
        scene.background = textSky;
        scene.fog = new THREE.Fog(0x6086b0, 600, 1000);
        renderer = new THREE.WebGLRenderer({antialias: true});
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);
        window.addEventListener('resize', onWindowResize, false);
        controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true; // an animation loop is required when either damping or auto-rotation are enabled
        controls.dampingFactor = 0.05;
        controls.screenSpacePanning = false;
        controls.minDistance = 50;
        controls.maxDistance = 500;
        controls.maxPolarAngle = Math.PI / 2 - 0.05;
    }

    function instanceToScene(mesh) {
        if (mesh !== undefined) {
            group.add(mesh.clone());
        }
        scene.add(group);
    }

    function createMuraille(Muraille, Rempart = null, textPath) {
        var geometry;
        var textMur = new THREE.TextureLoader().load(textPath);
        if (Rempart !== null) {
            var textRempart = new THREE.TextureLoader().load(textPath);
            textRempart.wrapS = THREE.RepeatWrapping;
            textRempart.wrapT = THREE.RepeatWrapping;
            textRempart.repeat.set(Rempart.Width / 20, Rempart.Height / 20);
            var materialRempart = new THREE.MeshBasicMaterial({map: textRempart});
        }
        textMur.wrapS = THREE.RepeatWrapping;
        textMur.wrapT = THREE.RepeatWrapping;
        textMur.repeat.set(Muraille.Width / 20, Muraille.Height / 20);
        var materialMur = new THREE.MeshBasicMaterial({map: textMur});


        var group = new THREE.Group();


        geometry = new THREE.CubeGeometry(Muraille.Width, Muraille.Height, Muraille.Depth);
        mesh = new THREE.Mesh(geometry, materialMur);
        mesh.position.y = Muraille.Height / 2;
        group.add(mesh.clone());

        if (Rempart !== null) {
            for (let initX = -(Muraille.Width / 2) + (Rempart.Width / 2); initX <= Muraille.Width / 2; initX += 20) {
                geometry = new THREE.CubeGeometry(Rempart.Width, Rempart.Height, Rempart.Depth);
                mesh = new THREE.Mesh(geometry, materialRempart);
                mesh.position.set(initX, Muraille.Height + Rempart.Height / 2, (Muraille.Depth - Rempart.Depth) / 2);
                group.add(mesh.clone());
                mesh.position.z = (-Muraille.Depth + Rempart.Depth) / 2;
                group.add(mesh.clone());
            }
        }
        return group;
    }

    function createTower(Tower, textMurPath, textToitPath, windowTextPath) {
        var group = new THREE.Group();
        var textMur = new THREE.TextureLoader().load(textMurPath);
        var textToit = new THREE.TextureLoader().load(textToitPath);
        var materialMur = new THREE.MeshBasicMaterial({map: textMur});
        var materialToit = new THREE.MeshBasicMaterial({map: textToit});
        if (Tower.WindowWidth !== undefined) {
            var textWindow = new THREE.TextureLoader().load(windowTextPath);
            var materialWindow = new THREE.MeshBasicMaterial({map: textWindow});
        }
        textMur.wrapS = THREE.RepeatWrapping;
        textMur.wrapT = THREE.RepeatWrapping;
        textMur.repeat.set(Tower.Radius / 5, Tower.Height / 20);
        textToit.wrapS = THREE.RepeatWrapping;
        textToit.wrapT = THREE.RepeatWrapping;
        textToit.repeat.set(2, 1);


        mesh = new THREE.Mesh(new THREE.CylinderBufferGeometry(Tower.Radius, Tower.Radius, Tower.Height,
            Tower.RadialSegment), materialMur);
        mesh.position.y = Tower.Height / 2;

        mesh2 = new THREE.Mesh(new THREE.CylinderBufferGeometry(Tower.RoofRadiusTop, Tower.RoofRadiusBottom,
            Tower.RoofHeight, Tower.RoofRadialSegment), materialToit);
        mesh2.position.y = Tower.Height + Tower.RoofHeight / 2;

        if (Tower.WindowWidth !== undefined) {
            mesh3 = new THREE.Mesh(new THREE.BoxGeometry(Tower.WindowWidth, Tower.WindowHeigth, Tower.WindowDepth),
                materialWindow);
            mesh3.position.set(0, Tower.Height - Tower.WindowHeigth / 2 - 5, Tower.Radius - Tower.WindowDepth / 2);
            group.add(mesh3.clone());
            mesh3.position.set(0, Tower.Height - Tower.WindowHeigth / 2 - 5, -Tower.Radius + Tower.WindowDepth / 2);
            group.add(mesh3.clone());
            mesh3.rotateY(Math.radians(90));
            mesh3.position.set(Tower.Radius - Tower.WindowDepth / 2, Tower.Height - Tower.WindowHeigth / 2 - 5, 0);
            group.add(mesh3.clone());
            mesh3.position.set(-Tower.Radius + Tower.WindowDepth / 2, Tower.Height - Tower.WindowHeigth / 2 - 5, 0);
            group.add(mesh3.clone());
        }

        group.add(mesh.clone());
        group.add(mesh2.clone());

        return group;
    }

    function createHouse(house, murPath, roofPath, windowPath, doorPath) {
        var group = new THREE.Group();
        var textMur = new THREE.TextureLoader().load(murPath);
        var textToit = new THREE.TextureLoader().load(roofPath);
        var textPorte = new THREE.TextureLoader().load(doorPath);
        var textWindow = new THREE.TextureLoader().load(windowPath);

        textMur.wrapS = THREE.RepeatWrapping;
        textMur.wrapT = THREE.RepeatWrapping;
        textMur.repeat.set(house.Width / 5, house.Height / 5);

        textToit.wrapS = THREE.RepeatWrapping;
        textToit.wrapT = THREE.RepeatWrapping;
        textToit.repeat.set(6, 3);

        textPorte.wrapS = THREE.RepeatWrapping;
        textPorte.wrapT = THREE.RepeatWrapping;
        textPorte.repeat.set(2, 1);

        var materialWindow = new THREE.MeshBasicMaterial({map: textWindow});
        var materialMur = new THREE.MeshBasicMaterial({map: textMur});
        var materialPorte = new THREE.MeshBasicMaterial({map: textPorte});
        var materialToit = new THREE.MeshBasicMaterial({map: textToit});


        mesh = new THREE.Mesh(new THREE.BoxGeometry(house.Width, house.Height, house.Depth), materialMur);
        mesh.position.set(0, house.Height / 2, 0);

        mesh2 = new THREE.Mesh(new THREE.PlaneGeometry(house.DoorWidth, house.DoorHeight), materialPorte);
        mesh2.position.set(0, house.DoorHeight / 2, house.Depth / 2 + 0.1);

        mesh3 = new THREE.Mesh(new THREE.CylinderBufferGeometry(
            house.RoofRadiusTop,
            house.RoofRadiusBottom,
            house.RoofHeight,
            house.RoofRadialSegment), materialToit);
        mesh3.position.y = house.Height + house.RoofHeight / 2;
        mesh3.rotateY(Math.radians(45));

        console.log(house.WindowHeight);
        console.log(house.WindowWidth);
        mesh4 = new THREE.Mesh(new THREE.BoxGeometry(house.WindowWidth, house.WindowHeight, house.WindowDepth),
            materialWindow);
        mesh4.rotateY(Math.radians(90));

        mesh4.position.set(house.Width / 2, house.Height - house.WindowHeight / 2 - 10, 0);
        group.add(mesh4.clone());
        mesh4.position.set(-house.Width / 2, house.Height - house.WindowHeight / 2 - 10, 0);
        group.add(mesh4.clone());

        group.add(mesh.clone());
        group.add(mesh2.clone());
        group.add(mesh3.clone());
        return group;
    }


    function onWindowResize() {

        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }

    function animate() {
        requestAnimationFrame(animate);
        //group.rotation.y += 0.005;
        renderer.render(scene, camera);
    }
</script>
</body>
</html>
