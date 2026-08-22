<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Tour - {{ $entity->name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS (from Vite or CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    @livewireStyles
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #000;
        }
        
        .vt-container {
            position: relative;
            width: 100vw;
            height: 100vh;
        }
        
        .vt-pano {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }
        
        .vt-sidebar {
            position: absolute;
            top: 0;
            right: 0;
            width: 380px;
            height: 100%;
            background: #fff;
            box-shadow: -5px 0 25px rgba(0,0,0,0.15);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .vt-sidebar {
                width: 100%;
                height: 60vh;
                top: auto;
                bottom: 0;
                box-shadow: 0 -5px 25px rgba(0,0,0,0.15);
                transform: translateY(100%);
            }
            .vt-sidebar[data-open="true"] {
                transform: translateY(0);
            }
            .vt-sidebar[data-open="false"] {
                transform: translateY(100%);
            }
            .vt-toggle-btn {
                bottom: 95px;
                right: 15px;
            }
        }
        
        @media (min-width: 769px) {
            .vt-sidebar[data-open="false"] {
                transform: translateX(100%);
            }
            .vt-sidebar[data-open="true"] {
                transform: translateX(0);
            }
            .vt-toggle-btn {
                top: 15px;
                right: 15px;
            }
        }
        
        .vt-toggle-btn {
            position: absolute;
            z-index: 90;
            background-color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            color: #1e293b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 10px 20px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .vt-toggle-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div class="vt-container" x-data="{ sidebarOpen: false }">
        <!-- 360 Panorama Iframe -->
        <div class="vt-pano">
            <!-- Back Button Overlay -->
            <a href="{{ route($domain . '.show', $slug) }}" class="btn btn-emerald text-white position-absolute shadow d-flex align-items-center justify-content-center" style="top: 15px; left: 15px; z-index: 90; background-color: #10b981; border-color: #10b981; border-radius: 8px; width: 44px; height: 44px; padding: 0;">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            
            <!-- Toggle Sidebar Button -->
            <button class="vt-toggle-btn" @click="sidebarOpen = true" x-show="!sidebarOpen" x-transition.opacity.duration.300ms>
                <i class="fa-solid fa-layer-group text-emerald me-2" style="color: #10b981;"></i> Layanan
            </button>
            
            <iframe src="{{ route('public.virtual-tour.serve', ['domain' => $domain, 'slug' => $slug, 'path' => 'index.html', 'raw' => 'true']) }}" style="width: 100%; height: 100%; border: none;" allowfullscreen allow="accelerometer; magnetometer; gyroscope"></iframe>
        </div>
        
        <!-- Overlay for mobile to close sidebar when clicking outside -->
        <div class="position-absolute w-100 h-100" style="z-index: 95; background: rgba(0,0,0,0.3);" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
        
        <!-- Sidebar -->
        <div class="vt-sidebar" :data-open="sidebarOpen.toString()">
            <div class="d-flex justify-content-end p-2 position-absolute" style="top: 0; right: 0; z-index: 110;">
                <button class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" @click="sidebarOpen = false">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <livewire:public.nearby-services :lat="$lat" :lng="$lng" :exclude-id="$entity->id" />
        </div>
    </div>

    @livewireScripts
</body>
</html>
