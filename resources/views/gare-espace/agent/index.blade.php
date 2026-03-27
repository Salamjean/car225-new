@extends('gare-espace.layouts.template')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
:root {
    --primary: #e94f1b;
    --primary-dark: #d33d0f;
    --primary-light: #fff7ed;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-900: #0f172a;
    --font-jakarta: 'Plus Jakarta Sans', sans-serif;
}

.gare-container {
    font-family: var(--font-jakarta);
    background-color: var(--gray-50);
    min-height: 100vh;
    width: 100%;
}

.stat-card-premium {
    background: white;
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.stat-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.table-premium {
    border-collapse: separate;
    border-spacing: 0 0.75rem;
}

.table-premium thead th {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 1rem 1.5rem;
}

.table-premium tbody tr {
    background: white;
    transition: all 0.2s;
}

.table-premium tbody tr td {
    border: none;
    padding: 1.25rem 1.5rem;
}

.table-premium tbody tr td:first-child {
    border-top-left-radius: 1.25rem;
    border-bottom-left-radius: 1.25rem;
    border-left: 1px solid var(--gray-100);
}

.table-premium tbody tr td:first-child {
    border-top-left-radius: 1.25rem;
    border-bottom-left-radius: 1.25rem;
    border-left: 1px solid var(--gray-100);
}

.table-premium tbody tr td:last-child {
    border-top-right-radius: 1.25rem;
    border-bottom-right-radius: 1.25rem;
    border-right: 1px solid var(--gray-100);
}

.table-premium tbody tr {
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.table-premium tbody tr:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    transform: scale(1.005);
    z-index: 10;
}

.badge-premium {
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-active { background: #dcfce7; color: #166534; }
.badge-archived { background: #fee2e2; color: #991b1b; }

.btn-premium-add {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 1rem;
    font-weight: 700;
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    transition: all 0.3s;
}

.btn-premium-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
    color: white;
}

.agent-avatar-premium {
    width: 48px;
    height: 48px;
    border-radius: 1rem;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.avatar-initials-premium {
    width: 48px;
    height: 48px;
    border-radius: 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
}
</style>

<div class="gare-container py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mx-auto flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div class="animate__animated animate__fadeInLeft">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-1">Agents de <span class="text-orange-600">Gare</span></h1>
            <p class="text-gray-500 font-medium">Gestion et pilotage des effectifs de votre gare</p>
        </div>
        <div class="animate__animated animate__fadeInRight">
            <a href="{{ route('gare-espace.agents.create') }}" class="btn btn-premium-add flex items-center gap-2">
                <i class="fas fa-plus-circle text-lg"></i>
                <span>Nouveau collaborateur</span>
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="stat-card-premium animate__animated animate__fadeInUp">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Effectif Total</p>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ $agents->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card-premium animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Agents Actifs</p>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ $agents->where('archived_at', null)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card-premium animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-archive"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Archivés</p>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ $agentsArchivedCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="mx-auto">
        <div class="bg-white/40 backdrop-blur-sm rounded-[2.5rem] p-4 lg:p-8 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
            @if($agents->isEmpty())
                <div class="py-20 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <i class="fas fa-user-slash text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Aucun agent assigné</h3>
                    <p class="text-gray-500 max-w-xs mx-auto mb-8">Votre gare ne compte aucun agent pour le moment. Commencez par en créer un.</p>
                    <a href="{{ route('gare-espace.agents.create') }}" class="btn-premium-add inline-flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Créer le premier agent
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full table-premium">
                        <thead>
                            <tr class="text-left">
                                <th>Collaborateur</th>
                                <th>Contact & Coordonnées</th>
                                <th>Localité</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                                <tr class="animate__animated animate__fadeIn">
                                    <td>
                                        <div class="flex items-center gap-4">
                                            @if($agent->profile_picture)
                                                <img src="{{ asset('storage/' . $agent->profile_picture) }}" class="agent-avatar-premium" alt="">
                                            @else
                                                <div class="avatar-initials-premium">
                                                    {{ strtoupper(substr($agent->name, 0, 1)) }}{{ strtoupper(substr($agent->prenom ?? '', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-black text-gray-900 leading-tight mb-0.5">{{ $agent->name }} {{ $agent->prenom }}</p>
                                                <p class="text-[11px] font-bold text-orange-500 uppercase tracking-tighter">ID: {{ $agent->code_id ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
                                                <i class="fas fa-envelope text-gray-300 w-4"></i>
                                                {{ $agent->email }}
                                            </div>
                                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
                                                <i class="fas fa-phone text-gray-300 w-4"></i>
                                                {{ $agent->contact }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
                                            <i class="fas fa-map-marker-alt text-orange-300"></i>
                                            {{ $agent->commune }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($agent->archived_at)
                                            <span class="badge-premium badge-archived">Archivé</span>
                                        @else
                                            <span class="badge-premium badge-active">Actif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2 px-2">
                                            <a href="{{ route('gare-espace.agents.edit', $agent) }}" 
                                               class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center hover:bg-orange-600 hover:text-white transition-all transform hover:-translate-y-1 shadow-sm"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all transform hover:-translate-y-1 shadow-sm archive-agent" 
                                                    data-id="{{ $agent->id }}"
                                                    title="Archiver">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <form id="archive-form-{{ $agent->id }}" action="{{ route('gare-espace.agents.destroy', $agent) }}" method="POST" class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Opération réussie',
            text: '{{ session('success') }}',
            timer: 4000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'rounded-2xl border-l-4 border-green-500'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur détectée',
            text: '{{ session('error') }}',
            confirmButtonColor: '#e94f1b',
            customClass: {
                popup: 'rounded-[2rem]'
            }
        });
    @endif

    document.querySelectorAll('.archive-agent').forEach(button => {
        button.addEventListener('click', function() {
            const agentId = this.dataset.id;
            
            Swal.fire({
                title: '<span class="text-2xl font-black">Archiver cet agent ?</span>',
                text: "L'agent sera désactivé et ne pourra plus accéder à son interface.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, archiver',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[2rem] p-8',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('archive-form-' + agentId).submit();
                }
            });
        });
    });
});
</script>
@endsection