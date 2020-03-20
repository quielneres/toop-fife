@if(Auth::user()->can('marketing'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary ">
            <i class="fa  fa-ge"></i><span> Eventos </span>
            <span class="menu-arrow"></span> </a>
        <ul class="list-unstyled">
            <li><a href="{{ route('evento.index') }}">Evento</a></li>
            <li><a href="{{ route('produtoExclusivo.index') }}">Produto exclusivo</a></li>
            <li><a href="{{ route('artista.index') }}">Artista</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('gestor_trade'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary ">
            <i class="fa  fa-trademark"></i><span> Trade </span>
            <span class="menu-arrow"></span> </a>
        <ul class="list-unstyled">
            <li><a href="{{route('trade.index')}}">Empresas</a></li>
            <li><a href="#" id="bloquear-cpf-trade">Bloquear CPF</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('atendimento'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa fa-child"></i><span> Usuários </span>
            <span class="menu-arrow"></span> </a>
        <ul class="list-unstyled">
            <li><a href="{{ route('usuario.index') }}">Usuário</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('atendimento'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa fa-line-chart"></i><span> Transações</span>
            <span class="menu-arrow"></span> </a>
        <ul class="list-unstyled">
            <li><a href="{{ route('transacoes.index') }}">Transações</a></li>
            <li><a href="#" id="novo-pedido-btn">Nova transação</a></li>
            <li><a href="#" id="transacao-lote">Cortesia em lote</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('financeiro'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa  fa-money"></i><span>Financeiro</span>
            <span class="menu-arrow"></span></a>
        <ul class="list-unstyled">
            <li><a href="{{ route('financeiro.index') }}">Arquivos TOTVS</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('gestor_credenciamento'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa fa-drivers-license-o"></i><span>Credenciamento</span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="list-unstyled">
            <li><a href="{{route('credenciamento.index')}}">Empresas</a></li>
            <li><a href="#" id="validar-credencial">Validar credencial</a></li>
            <li><a href="#" class="bloquear-cpf">Bloquear CPF</a></li>
            <li><a href="{{ route('credenciamento.usuarios-bloqueados') }}">Usuarios bloqueados</a></li>
            <li><a href="{{route('credenciamento.relatorio_geral')}}">Relatório geral</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('representante_empresa'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa fa-drivers-license-o"></i><span>Credenciamento</span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="list-unstyled">
            <li><a href="{{route('credenciamento.relatorio_empresa', ['uuid' => Auth::user()->uuid])}}">Relatório da
                    empresa</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('super'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa fa-laptop"></i><span>Edição do site</span>
            <span class="menu-arrow"></span> </a>
        <ul class="list-unstyled">
            <li><a href="{{ route('site.index') }}">Editar</a></li>
        </ul>
    </li>
@endif

@if(Auth::user()->can('super'))
    <li class="has_sub">
        <a href="#" class="waves-effect waves-primary">
            <i class="fa fa-television"></i><span>TV</span>
            <span class="menu-arrow"></span> </a>
        <ul class="list-unstyled">
            <li><a href="{{ route('tv.index') }}">Real time tvs</a></li>
        </ul>
    </li>
@endif
