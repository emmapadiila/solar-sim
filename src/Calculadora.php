<?php
/**
 * Modelo de cálculo de la simulación solar.
 *
 * Es la única fuente de verdad: el frontend pide los resultados a
 * api/simulaciones/calcular.php y al guardar se vuelven a calcular aquí,
 * así nunca se almacenan cifras enviadas por el navegador.
 */
final class Calculadora
{
    /** Irradiación solar por ciudad (horas sol pico, kWh/m²/día). */
    public const CIUDADES = [
        'bogota'        => ['nombre' => 'Bogotá',        'irradiacion' => 4.2],
        'medellin'      => ['nombre' => 'Medellín',      'irradiacion' => 4.8],
        'cali'          => ['nombre' => 'Cali',          'irradiacion' => 5.1],
        'barranquilla'  => ['nombre' => 'Barranquilla',  'irradiacion' => 5.5],
        'cartagena'     => ['nombre' => 'Cartagena',     'irradiacion' => 5.7],
        'bucaramanga'   => ['nombre' => 'Bucaramanga',   'irradiacion' => 4.9],
        'pereira'       => ['nombre' => 'Pereira',       'irradiacion' => 4.6],
        'manizales'     => ['nombre' => 'Manizales',     'irradiacion' => 4.4],
        'ibague'        => ['nombre' => 'Ibagué',        'irradiacion' => 4.7],
        'villavicencio' => ['nombre' => 'Villavicencio', 'irradiacion' => 4.5],
    ];

    /** Precio del kWh por estrato (COP). */
    public const PRECIO_KWH = [1 => 450, 2 => 520, 3 => 580, 4 => 650, 5 => 720, 6 => 800];

    public const TIPOS_ENERGIA = [
        'convencional' => 'Convencional',
        'renovable'    => 'Renovable',
        'mixta'        => 'Mixta',
    ];

    public const CONSUMO_MIN = 50;
    public const CONSUMO_MAX = 2000;
    public const AREA_MIN = 10;
    public const AREA_MAX = 500;

    private const VATIOS_PANEL = 400;       // W por panel
    private const AREA_PANEL = 1.7;         // m² por panel
    private const DIAS_MES = 30.44;
    private const FACTOR_PERDIDAS = 0.91;   // pérdidas del sistema
    private const COSTO_POR_KW = 3500000;   // COP por kW instalado

    public static function nombreCiudad(string $clave): string
    {
        return self::CIUDADES[$clave]['nombre'] ?? ucfirst($clave);
    }

    /**
     * Valida y normaliza los datos de entrada.
     * @return array{0: array, 1: string|null} [datos normalizados, mensaje de error]
     */
    public static function validar(array $entrada): array
    {
        $datos = [
            'ubicacion'       => (string)($entrada['ubicacion'] ?? ''),
            'estrato'         => filter_var($entrada['estrato'] ?? null, FILTER_VALIDATE_INT),
            'consumo_mensual' => filter_var($entrada['consumo_mensual'] ?? null, FILTER_VALIDATE_FLOAT),
            'area_disponible' => filter_var($entrada['area_disponible'] ?? null, FILTER_VALIDATE_FLOAT),
            'tipo_energia'    => (string)($entrada['tipo_energia'] ?? ''),
        ];

        if (!isset(self::CIUDADES[$datos['ubicacion']])) {
            return [$datos, 'Por favor selecciona una ubicación válida'];
        }
        if (!isset(self::PRECIO_KWH[$datos['estrato']])) {
            return [$datos, 'Por favor selecciona un estrato válido'];
        }
        if ($datos['consumo_mensual'] === false || $datos['consumo_mensual'] < self::CONSUMO_MIN || $datos['consumo_mensual'] > self::CONSUMO_MAX) {
            return [$datos, sprintf('El consumo mensual debe estar entre %d y %d kWh', self::CONSUMO_MIN, self::CONSUMO_MAX)];
        }
        if ($datos['area_disponible'] === false || $datos['area_disponible'] < self::AREA_MIN || $datos['area_disponible'] > self::AREA_MAX) {
            return [$datos, sprintf('El área disponible debe estar entre %d y %d m²', self::AREA_MIN, self::AREA_MAX)];
        }
        if (!isset(self::TIPOS_ENERGIA[$datos['tipo_energia']])) {
            return [$datos, 'Por favor selecciona el tipo de energía'];
        }

        return [$datos, null];
    }

    /** Calcula los resultados a partir de datos ya validados. */
    public static function calcular(array $datos): array
    {
        $irradiacion = self::CIUDADES[$datos['ubicacion']]['irradiacion'];
        $consumo = (float)$datos['consumo_mensual'];
        $area = (float)$datos['area_disponible'];

        $energiaPanelMes = (self::VATIOS_PANEL * $irradiacion / 1000) * self::DIAS_MES;

        $panelesNecesarios = (int)ceil(($consumo / self::FACTOR_PERDIDAS) / $energiaPanelMes);
        $panelesQueCaben = (int)floor($area / self::AREA_PANEL);
        $panelesAInstalar = min($panelesNecesarios, $panelesQueCaben);

        $energiaGenerada = $panelesAInstalar * $energiaPanelMes * self::FACTOR_PERDIDAS;
        $cobertura = min(100, ($energiaGenerada / $consumo) * 100);

        $areaTexto = formato_numero($area, fmod($area, 1) ? 1 : 0);
        $coberturaTexto = formato_numero($cobertura, 1);

        if ($panelesQueCaben >= $panelesNecesarios) {
            $mensaje = sprintf(
                'Tu techo de %s m² es suficiente para instalar %d paneles y cubrir aproximadamente el %s %% de tu consumo.',
                $areaTexto, $panelesAInstalar, $coberturaTexto
            );
        } else {
            $mensaje = sprintf(
                'En %s m² caben %d paneles, que cubren aproximadamente el %s %% de tu consumo. Para cubrir el 100 %% necesitarías %d paneles.',
                $areaTexto, $panelesAInstalar, $coberturaTexto, $panelesNecesarios
            );
        }

        $ahorroMensual = min($energiaGenerada, $consumo) * self::PRECIO_KWH[$datos['estrato']];
        $ahorroAnual = $ahorroMensual * 12;
        $costoInstalacion = ($panelesAInstalar * self::VATIOS_PANEL / 1000) * self::COSTO_POR_KW;
        $retorno = $ahorroAnual > 0 ? $costoInstalacion / $ahorroAnual : 0;

        return [
            'energiaGenerada'     => round($energiaGenerada, 2),
            'ahorroMensual'       => (int)round($ahorroMensual),
            'ahorroAnual'         => (int)round($ahorroAnual),
            'retornoInversion'    => round($retorno, 1),
            'costoInstalacion'    => (int)round($costoInstalacion),
            'panelesNecesarios'   => $panelesNecesarios,
            'panelesInstalables'  => $panelesAInstalar,
            'porcentajeCobertura' => round($cobertura, 1),
            'mensajePaneles'      => $mensaje,
        ];
    }
}
