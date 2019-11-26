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

    Math.radians = function (degrees) {
        return degrees * Math.PI / 180;
    };
    var camera, scene, renderer;
    var terrain, mesh, mesh2, mesh3, mesh4, mesh5, mesh6;
    var group = new THREE.Group();
    init();
    animate();

    function init() {

        scene = new THREE.Scene();

        camera = new THREE.PerspectiveCamera(70, window.innerWidth / window.innerHeight, 1, 3000);
        camera.position.z = 200;
        camera.position.y = 100;
        camera.rotation.x -= 0.45;
        var geometry;

        /**
         * Textures matériel
         */

        var textMur = new THREE.TextureLoader().load('./tex/stone wall 7.png');
        var textLMur = new THREE.TextureLoader().load('./tex/stone wall 7.png');
        var textRempart = new THREE.TextureLoader().load('./tex/stone wall 7.png');
        var textGrass = new THREE.TextureLoader().load('./tex/grass1.png');
        var textPorte = new THREE.TextureLoader().load('./tex/door.jpg');
        var textGate = new THREE.TextureLoader().load('./tex/stone wall 7.png');
        var textToit = new THREE.TextureLoader().load('./tex/roof.jpg');
        var textWindow = new THREE.TextureLoader().load('./tex/window.jpg');
        var textSky = new THREE.TextureLoader().load('./tex/sky.jpg');

        textRempart.wrapS = THREE.RepeatWrapping;
        textRempart.wrapT = THREE.RepeatWrapping;
        textRempart.repeat.set(0.5, 0.25);
        textLMur.wrapS = THREE.RepeatWrapping;
        textLMur.wrapT = THREE.RepeatWrapping;
        textLMur.repeat.set(10, 2);
        textMur.wrapS = THREE.RepeatWrapping;
        textMur.wrapT = THREE.RepeatWrapping;
        textMur.repeat.set(4, 2);
        textGate.wrapS = THREE.RepeatWrapping;
        textGate.wrapT = THREE.RepeatWrapping;
        textGate.repeat.set(2, 2);
        textToit.wrapS = THREE.RepeatWrapping;
        textToit.wrapT = THREE.RepeatWrapping;
        textToit.repeat.set(2, 1);
        textGrass.wrapS = THREE.RepeatWrapping;
        textGrass.wrapT = THREE.RepeatWrapping;
        textGrass.repeat.set(80, 80);

        var materialLMur = new THREE.MeshBasicMaterial({map: textLMur});
        var materialMur = new THREE.MeshBasicMaterial({map: textMur});
        var materialRempart = new THREE.MeshBasicMaterial({map: textRempart});
        var materialGrass = new THREE.MeshBasicMaterial({map: textGrass});
        var materialPorte = new THREE.MeshBasicMaterial({map: textPorte});
        var materialGate = new THREE.MeshBasicMaterial({map: textGate});
        var materialToit = new THREE.MeshBasicMaterial({map: textToit});
        var materialWindow = new THREE.MeshBasicMaterial({map: textWindow});

        /**
         * Taille des objects
         */
            //front muraille
        var FrontMurailleWidth = 100;
        //muraille
        var MurailleWidth = 310;
        var MurailleHeight = 40;
        var MurailleDepth = 10;

        //remparts
        var RempartWidth = 6;
        var RempartHeight = 3;
        var RempartDepth = 2;

        //Entrée + porte
        var GateWidth = 40;
        var GateHeight = 50;
        var GateDepth = 40;
        var GateRoofRadiusTop = 0;
        var GateRoofRadiusBottom = 28;
        var GateRoofHeight = 20;
        var GateRoofRadialSegment = 4;
        var DoorWidth = 40;
        var DoorHeight = 40;

        /**
         * Ajout du terrain
         */
        geometry = new THREE.PlaneGeometry(3000, 3000);
        terrain = new THREE.Mesh(geometry, materialGrass);
        terrain.rotateX(Math.radians(-90));
        scene.add(terrain);


        /**
         * Muraille avant
         */
        var frontMuraille = new THREE.Group();

        geometry = new THREE.CubeGeometry(FrontMurailleWidth, MurailleHeight, MurailleDepth);
        mesh = new THREE.Mesh(geometry, materialMur);
        frontMuraille.add(mesh.clone());

        for (let initX = -(FrontMurailleWidth / 2) + (RempartWidth / 2); initX <= FrontMurailleWidth / 2; initX += 20) {
            geometry = new THREE.CubeGeometry(RempartWidth, RempartHeight, RempartDepth);
            mesh = new THREE.Mesh(geometry, materialRempart);
            mesh.position.set(initX, (MurailleHeight + RempartHeight) / 2, (MurailleDepth - RempartDepth) / 2);
            frontMuraille.add(mesh.clone());
            mesh.position.z = (-MurailleDepth + RempartDepth) / 2;
            frontMuraille.add(mesh.clone());
        }

        /**
         * Muraille longue
         */
        var large_muraille = new THREE.Group();
        geometry = new THREE.CubeGeometry(MurailleWidth, MurailleHeight, MurailleDepth);
        mesh = new THREE.Mesh(geometry, materialLMur);
        large_muraille.add(mesh.clone());

        for (let initX = -(MurailleWidth / 2) + (RempartWidth / 2); initX <= MurailleWidth / 2; initX += 20) {
            geometry = new THREE.CubeGeometry(RempartWidth, RempartHeight, RempartDepth);
            mesh = new THREE.Mesh(geometry, materialRempart);
            mesh.position.set(initX, (MurailleHeight + RempartHeight) / 2, (MurailleDepth - RempartDepth) / 2);
            large_muraille.add(mesh.clone());
            mesh.position.z = (-MurailleDepth + RempartDepth) / 2;
            large_muraille.add(mesh.clone());
        }

        /**
         * Entrée + porte
         */
        var entree = new THREE.Group();
        mesh = new THREE.Mesh(new THREE.BoxGeometry(GateWidth, GateHeight, GateDepth), materialGate);
        mesh.position.set(0, GateHeight / 2, 0);

        geometry = new THREE.PlaneGeometry(DoorWidth, DoorHeight);
        mesh2 = new THREE.Mesh(geometry, materialPorte);
        mesh2.position.set(0, DoorHeight / 2, GateDepth / 2 + 0.1);

        mesh3 = new THREE.Mesh(new THREE.CylinderBufferGeometry(
            GateRoofRadiusTop,
            GateRoofRadiusBottom,
            GateRoofHeight,
            GateRoofRadialSegment), materialToit);
        mesh3.position.y = GateHeight + GateRoofHeight / 2;
        mesh3.rotateY(Math.radians(45));

        entree.add(mesh.clone());
        entree.add(mesh2.clone());
        entree.add(mesh3.clone());
        /**
         * Tour
         */

        var tour = new THREE.Group();
        mesh = new THREE.Mesh(new THREE.CylinderBufferGeometry(20, 20, 70, 32), materialMur);
        mesh.position.y = 35;

        mesh2 = new THREE.Mesh(new THREE.CylinderBufferGeometry(0, 25, 20, 32), materialToit);
        mesh2.position.y = 80;

        mesh3 = new THREE.Mesh(new THREE.BoxGeometry(10, 10, 3), materialWindow);
        mesh3.position.set(0, 60, 18.5);
        tour.add(mesh3.clone());
        mesh3.position.set(0, 60, -18.5);
        tour.add(mesh3.clone());
        mesh3.rotateY(Math.radians(90));
        mesh3.position.set(18.5, 60, 0);
        tour.add(mesh3.clone());
        mesh3.position.set(-18.5, 60, 0);
        tour.add(mesh3.clone());

        tour.add(mesh.clone());
        tour.add(mesh2.clone());

        /**
         * Donjon
         */

        var donjon = new THREE.Group();
        mesh = new THREE.Mesh(new THREE.CylinderBufferGeometry(40, 40, 200, 32), materialMur);
        mesh.position.y = 0;

        mesh2 = new THREE.Mesh(new THREE.CylinderBufferGeometry(0, 50, 70, 32), materialToit);
        mesh2.position.y = 130;
        donjon.add(mesh.clone());
        donjon.add(mesh2.clone());


        /**
         * Affichage
         */
        donjon.position.set(0, 0, 0);
        instanceToScene(donjon);

        tour.position.set(-140, 0, 115);
        instanceToScene(tour);
        tour.position.set(140, 0, 115);
        instanceToScene(tour);
        tour.position.set(-140, 0, -200);
        instanceToScene(tour);
        tour.position.set(140, 0, -200);
        instanceToScene(tour);

        frontMuraille.position.set(-70, 20, 115);
        instanceToScene(frontMuraille);
        frontMuraille.position.set(70, 20, 115);
        instanceToScene(frontMuraille);


        large_muraille.position.set(0, 20, -200);
        instanceToScene(large_muraille);
        large_muraille.position.set(-135, 20, -30);
        large_muraille.rotation.y = Math.radians(90);
        instanceToScene(large_muraille);
        large_muraille.position.set(145, 20, -30);
        instanceToScene(large_muraille);

        entree.position.set(0, 0, 100);
        instanceToScene(entree);
        group.add(terrain);

        /**
         * Options de rendu
         */
        scene.background = textSky;
        renderer = new THREE.WebGLRenderer({antialias: true});
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);
        window.addEventListener('resize', onWindowResize, false);
    }

    function instanceToScene(mesh) {
        if (mesh !== undefined) {
            group.add(mesh.clone());
        }
        scene.add(group);
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
