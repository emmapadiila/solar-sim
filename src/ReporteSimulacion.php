<?php
/**
 * Reporte en PDF de una simulación guardada (lo descarga api/simulaciones/exportar.php).
 * Usa FPDF (src/lib/fpdf), sin dependencias externas.
 *
 * Los datos guardados (ahorro, energía, retorno) salen tal cual de la base de datos;
 * paneles, cobertura e inversión se recalculan con Calculadora porque no se almacenan.
 */
final class ReporteSimulacion extends FPDF
{
    // Paleta de la app (RGB)
    private const NAVY = [15, 29, 51];
    private const SOL = [245, 158, 11];
    private const VERDE = [4, 120, 87];
    private const VERDE_FONDO = [236, 253, 245];
    private const VERDE_BORDE = [167, 243, 208];
    private const TEXTO = [15, 23, 42];
    private const GRIS = [100, 116, 139];
    private const GRIS_CLARO = [203, 213, 225];
    private const BORDE = [226, 232, 240];

    private const MARGEN = 18;
    private const ANCHO = 174; // 210 mm de A4 menos los márgenes

    private array $simulacion;
    private array $calculo;

    public function __construct(array $simulacion)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->simulacion = $simulacion;
        $this->calculo = Calculadora::calcular([
            'ubicacion'       => $simulacion['ubicacion'],
            'estrato'         => (int)$simulacion['estrato'],
            'consumo_mensual' => (float)$simulacion['consumo_mensual'],
            'area_disponible' => (float)$simulacion['area_disponible'],
        ]);

        $this->SetTitle($this->t('Simulación solar #' . $simulacion['id_simulacion']));
        $this->SetAuthor('SolarSim');
        $this->SetMargins(self::MARGEN, 50, self::MARGEN);
        $this->SetAutoPageBreak(true, 22);
        $this->AliasNbPages();
    }

    /** Genera el documento completo. */
    public function generar(): self
    {
        $this->AddPage();
        $this->resultadoPrincipal();
        $this->seccionDatos();
        $this->seccionDetalle();
        $this->grafico();
        $this->referencias();
        return $this;
    }

    // ---------- Cabecera y pie (FPDF los llama en cada página) ----------

    public function Header(): void
    {
        $s = $this->simulacion;

        $this->relleno(self::NAVY);
        $this->Rect(0, 0, 210, 36, 'F');

        // Marca: cuadrado ámbar con un "sol" navy
        $this->relleno(self::SOL);
        $this->Rect(self::MARGEN, 11, 11, 11, 'F');
        $this->relleno(self::NAVY);
        $this->Rect(self::MARGEN + 3.5, 14.5, 4, 4, 'F');

        $this->SetXY(self::MARGEN + 15, 10.5);
        $this->fuente('B', 17, [255, 255, 255]);
        $this->Cell(80, 8, 'SolarSim');
        $this->SetXY(self::MARGEN + 15, 18.5);
        $this->fuente('', 9.5, self::GRIS_CLARO);
        $this->Cell(90, 5, $this->t('Reporte de simulación de paneles solares'));

        $this->SetXY(110, 11.5);
        $this->fuente('B', 10, [255, 255, 255]);
        $this->Cell(82, 5, $this->t('Simulación #' . $s['id_simulacion']), 0, 2, 'R');
        $this->fuente('', 9, self::GRIS_CLARO);
        $this->Cell(82, 5, $this->t(date('d/m/Y · H:i', strtotime($s['fecha']))), 0, 0, 'R');

        $this->SetY(48);
    }

    public function Footer(): void
    {
        $this->SetY(-16);
        $this->trazo(self::BORDE);
        $this->Line(self::MARGEN, $this->GetY(), 210 - self::MARGEN, $this->GetY());
        $this->Ln(2.5);
        $this->fuente('', 7.5, self::GRIS);
        $this->Cell(140, 4, $this->t('Estimación de referencia. Los valores reales dependen del equipo, la orientación del techo y las tarifas vigentes.'));
        $this->Cell(34, 4, $this->t('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }

    // ---------- Secciones ----------

    private function resultadoPrincipal(): void
    {
        $s = $this->simulacion;
        $this->titulo('Resultado');

        $y = $this->GetY();
        $this->relleno(self::VERDE_FONDO);
        $this->trazo(self::VERDE_BORDE);
        $this->Rect(self::MARGEN, $y, self::ANCHO, 32, 'DF');

        $this->SetXY(self::MARGEN + 7, $y + 5);
        $this->fuente('B', 10, self::VERDE);
        $this->Cell(100, 5, 'Ahorro mensual estimado', 0, 2);
        $this->fuente('B', 26, self::VERDE);
        $this->Cell(100, 12, formato_moneda($s['ahorro_mensual']), 0, 2);
        $this->fuente('', 9.5, self::GRIS);
        $this->Cell(160, 5, $this->t(sprintf(
            '%s al año · la inversión se recupera en %s años',
            formato_moneda($s['ahorro_anual']),
            formato_numero($s['retorno_inversion'], 1)
        )));

        $this->SetY($y + 32 + 9);
    }

    private function seccionDatos(): void
    {
        $s = $this->simulacion;
        $this->titulo('Datos de la vivienda');
        $this->rejilla([
            ['Ciudad', Calculadora::nombreCiudad($s['ubicacion'])],
            ['Estrato', (string)$s['estrato']],
            ['Tipo de energía', ucfirst($s['tipo_energia'])],
            ['Consumo mensual', formato_numero($s['consumo_mensual']) . ' kWh'],
            ['Área disponible', formato_numero($s['area_disponible']) . ' m²'],
            ['Precio del kWh', formato_moneda(Calculadora::PRECIO_KWH[(int)$s['estrato']] ?? 0)],
        ]);
    }

    private function seccionDetalle(): void
    {
        $c = $this->calculo;
        $this->titulo('Detalle técnico');
        $this->rejilla([
            ['Energía generada', formato_numero($this->simulacion['energia_generada'], 1) . ' kWh/mes'],
            ['Paneles necesarios', (string)$c['panelesNecesarios']],
            ['Paneles que caben', (string)$c['panelesInstalables']],
            ['Cobertura del consumo', formato_numero($c['porcentajeCobertura'], 1) . ' %'],
            ['Inversión estimada', formato_moneda($c['costoInstalacion'])],
            ['Irradiación solar', formato_numero(Calculadora::CIUDADES[$this->simulacion['ubicacion']]['irradiacion'] ?? 0, 1) . ' h sol/día'],
        ]);
    }

    private function grafico(): void
    {
        $consumo = (float)$this->simulacion['consumo_mensual'];
        $generacion = (float)$this->simulacion['energia_generada'];
        $maximo = max($consumo, $generacion, 1);

        $this->titulo('Consumo vs. generación');

        $barras = [
            ['Consumo actual', $consumo, self::NAVY],
            ['Generación solar', $generacion, self::SOL],
        ];
        $anchoEtiqueta = 36;
        $anchoMax = self::ANCHO - $anchoEtiqueta - 30;

        foreach ($barras as [$etiqueta, $valor, $color]) {
            $y = $this->GetY();
            $this->SetXY(self::MARGEN, $y + 1);
            $this->fuente('', 9.5, self::TEXTO);
            $this->Cell($anchoEtiqueta, 6, $this->t($etiqueta));

            $this->relleno([241, 245, 249]);
            $this->Rect(self::MARGEN + $anchoEtiqueta, $y + 1, $anchoMax, 6, 'F');
            $this->relleno($color);
            $this->Rect(self::MARGEN + $anchoEtiqueta, $y + 1, max(1, $anchoMax * $valor / $maximo), 6, 'F');

            $this->SetXY(self::MARGEN + $anchoEtiqueta + $anchoMax + 3, $y + 1);
            $this->fuente('B', 9.5, self::TEXTO);
            $this->Cell(27, 6, $this->t(formato_numero($valor, fmod($valor, 1) ? 1 : 0) . ' kWh'));
            $this->SetY($y + 10);
        }
        $this->Ln(4);
    }

    private function referencias(): void
    {
        $this->titulo('Referencias');
        $this->fuente('', 9.5, self::GRIS);
        foreach ([
            'Un hogar de 4 personas consume en promedio 350 kWh al mes.',
            'Una instalación básica requiere al menos 20 m² de techo libre de sombras.',
            'El retorno típico de la inversión está entre 5 y 8 años.',
            'La vida útil de un panel solar es de 25 a 30 años.',
        ] as $linea) {
            $this->SetX(self::MARGEN);
            $this->relleno(self::SOL);
            $this->Rect(self::MARGEN + 1, $this->GetY() + 2, 1.6, 1.6, 'F');
            $this->SetX(self::MARGEN + 5);
            $this->MultiCell(self::ANCHO - 5, 5.5, $this->t($linea));
        }
    }

    // ---------- Piezas reutilizables ----------

    /** Etiqueta de sección en mayúsculas con una línea debajo. */
    private function titulo(string $texto): void
    {
        $this->SetX(self::MARGEN);
        $this->fuente('B', 8.5, self::GRIS);
        $this->Cell(self::ANCHO, 5, $this->t($this->mayusculas($texto)), 0, 1);
        $this->trazo(self::BORDE);
        $this->Line(self::MARGEN, $this->GetY() + 0.5, self::MARGEN + self::ANCHO, $this->GetY() + 0.5);
        $this->Ln(4);
    }

    /** Rejilla de 3 columnas con recuadros etiqueta/valor. */
    private function rejilla(array $datos): void
    {
        $gap = 4;
        $ancho = (self::ANCHO - 2 * $gap) / 3;
        $alto = 16;
        $yInicio = $this->GetY();

        foreach (array_values($datos) as $i => [$etiqueta, $valor]) {
            $x = self::MARGEN + ($i % 3) * ($ancho + $gap);
            $y = $yInicio + intdiv($i, 3) * ($alto + $gap);

            $this->trazo(self::BORDE);
            $this->Rect($x, $y, $ancho, $alto, 'D');

            $this->SetXY($x + 4, $y + 3);
            $this->fuente('B', 7, self::GRIS);
            $this->Cell($ancho - 8, 4, $this->t($this->mayusculas($etiqueta)), 0, 2);
            $this->fuente('B', 11, self::TEXTO);
            $this->Cell($ancho - 8, 6, $this->t($valor));
        }

        $filas = (int)ceil(count($datos) / 3);
        $this->SetY($yInicio + $filas * ($alto + $gap) + 5);
    }

    private function fuente(string $estilo, float $tamano, array $color): void
    {
        $this->SetFont('Helvetica', $estilo, $tamano);
        $this->SetTextColor(...$color);
    }

    private function relleno(array $color): void
    {
        $this->SetFillColor(...$color);
    }

    private function trazo(array $color): void
    {
        $this->SetDrawColor(...$color);
        $this->SetLineWidth(0.3);
    }

    /** Mayúsculas para etiquetas, respetando la unidad kWh. */
    private function mayusculas(string $texto): string
    {
        return str_replace('KWH', 'kWh', mb_strtoupper($texto));
    }

    /** FPDF usa Windows-1252 en sus fuentes estándar: convierte tildes, ñ, ², ·. */
    private function t(string $texto): string
    {
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $texto);
    }
}
