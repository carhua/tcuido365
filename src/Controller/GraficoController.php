<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Controller;

use App\Entity\CasoDesaparecido;
use App\Entity\CasoDesproteccion;
use App\Entity\CasoTrata;
use App\Entity\CasoViolencia;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\Usuario;
use App\Enum\LeyendaEnum;
use App\Security\Security;
use App\Traits\TraitUser;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/grafico')]
final class GraficoController extends BaseController
{
    use TraitUser;

    #[Route(path: '/total', name: 'grafico_total', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::LIST, 'grafico_total');
        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $usuarioAlias = $request->query->get('usuario');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        /** @var Usuario $user */
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $centro = null;
        $provincia = $oprovincia ?? $user->getProvincia();
        $distrito = $odistrito ?? $user->getDistrito();
        if ('ROLE_TENIENTE' === $role) {
            $centro = $ocentro ?? $user->getCentroPoblado();
        }

        $data = $this->dataCasosAnios($em, $provincia, $distrito, $centro, $usuarioAlias);

        $roles = self::validarRoles($user->getRoles());
        $provincias = self::listProvinciasByRol($roles, $user, $em);

        $usuarios = [];
        if ($this->isSuperAdmin()) {
            $usuarios = $em->getRepository(Usuario::class)->allNombres();
        }

        return $this->render('agraficos/grafico_total.html.twig',
            [
                'provincias' => $provincias,
//                'provinciaId' => $provinciaId,
                'odistrito' => $odistrito,
                'ocentro' => $ocentro,
                'usuarios' => $usuarios,
                'dataAnual' => $data,
            ]
        );
    }

    public function dataCasosAnios(
        EntityManagerInterface $em,
        ?Provincia $provincia,
        ?Distrito $distrito,
        ?CentroPoblado $centro,
        ?string $usuario,
    ): array {
        $casoscv = $em->getRepository(CasoViolencia::class)->filterChartPorAnios($provincia, $distrito, $centro, $usuario);
        $casosde = $em->getRepository(CasoDesproteccion::class)->filterChartPorAnios($provincia, $distrito, $centro, $usuario);
        $casostp = $em->getRepository(CasoTrata::class)->filterChartPorAnios($provincia, $distrito, $centro, $usuario);
        $casospd = $em->getRepository(CasoDesaparecido::class)->filterChartPorAnios($provincia, $distrito, $centro, $usuario);

        $data = [];
        $anios = [];
        $item = [];
        $item['caso'] = 'VIOLENCIA';
        foreach ($casoscv as $value) {
            $item[$value['anio']] = $value['cantidad'];
            $anios[$value['anio']] = (string) $value['anio'];
        }
        $data[] = $item;

        $item = [];
        $item['caso'] = 'DESPROTECCIÓN';
        foreach ($casosde as $value) {
            $item[$value['anio']] = $value['cantidad'];
            $anios[$value['anio']] = (string) $value['anio'];
        }
        $data[] = $item;

        $item = [];
        $item['caso'] = 'TRATA DE PERSONAS';
        foreach ($casostp as $value) {
            $item[$value['anio']] = $value['cantidad'];
            $anios[$value['anio']] = (string) $value['anio'];
        }
        $data[] = $item;

        $item = [];
        $item['caso'] = 'DESAPARECIDO';
        foreach ($casospd as $value) {
            $item[$value['anio']] = $value['cantidad'];
            $anios[$value['anio']] = (string) $value['anio'];
        }
        $data[] = $item;

        return [
            'values' => $data,
            'category' => 'caso',
            'series' => array_values($anios),
        ];
    }

    #[Route(path: '/total/descargar', name: 'grafico_total_descargar', methods: ['GET'])]
    public function descargar(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccess(Security::EXPORT, 'grafico_total');

        $distritoId = $request->query->get('distrito');
        $provinciaId = $request->query->get('provincia');
        $centroId = $request->query->get('centroPoblado');
        $usuarioAlias = $request->query->get('usuario');
        $odistrito = null === $distritoId ? null : $em->getRepository(Distrito::class)->findOneBy(['isActive' => true, 'id' => $distritoId]);
        $oprovincia = null === $provinciaId ? null : $em->getRepository(Provincia::class)->findOneBy(['isActive' => true, 'id' => $provinciaId]);
        $ocentro = null === $centroId ? null : $em->getRepository(CentroPoblado::class)->findOneBy(['isActive' => true, 'id' => $centroId]);

        $data = $this->dataCasosAnios($em, $oprovincia, $odistrito, $ocentro, $usuarioAlias);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CASOS TOTALES');
        $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
        $sheet->setCellValue('A2', 'FECHA DE REPORTE: '.(new \DateTime())->format('Y-m-d'));
        $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
        $sheet->setCellValue('A3', 'Cantidad Registros: '.\count($data));
        $spreadsheet->getActiveSheet()->mergeCells('A3:J3');

        // estableciendo estilos de titulos
        $styleArrayTitulo = [
            'font' => [
                'bold' => true,
                'size' => '14',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArrayTitulo);
        $spreadsheet->getActiveSheet()->getStyle('A1:B3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F2F2F2');

        // agregando logo tcuido
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./img/tcuido_logo.png');
        $drawing->setWidth(95);
        $drawing->setHeight(75);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        // establecer las cabeceras
        $sheet->setCellValue('A5', 'CASO');
        $column = 2;
        foreach ($data['series'] as $dato) {
            $sheet->setCellValue([$column++, 5], $dato);
        }

        // estilos para las cabeceras
        $styleArrayCabeceras = [
            'font' => [
                'bold' => true,
                'size' => '11',
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];

        $numColumns = \count($data['series']) + 1;
        $spreadsheet->getActiveSheet()->getStyle([1, 5, $numColumns, 5])->applyFromArray($styleArrayCabeceras);
        $spreadsheet->getActiveSheet()->getStyle([1, 5, $numColumns, 5])->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('215967');
        $spreadsheet->getActiveSheet()->setAutoFilter([1, 5, $numColumns, 5]);

        $i = 6;
        foreach ($data['values'] as $dato) {
            $column = 1;
            $sheet->setCellValue([$column++, $i], $dato['caso']);
            foreach ($data['series'] as $item) {
                $sheet->setCellValue([$column++, $i], $dato[(int) $item] ?? null);
            }
            ++$i;
        }

        // estableciendo anchos de columa
        for ($col = 65; $col <= 78; ++$col) {
            $spreadsheet->getActiveSheet()->getColumnDimension(\chr($col))->setAutoSize(true);
        }

        $sheet->setCellValue([1, $i + 1], LeyendaEnum::DATOS_CONFIDENCIALES);
        $spreadsheet->getActiveSheet()->getStyle([1, $i + 1])->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F2F2F2');

        $sheet->setTitle('CASOS');
        $writer = new Xlsx($spreadsheet);
        $filename = tempnam(sys_get_temp_dir(), 'exportar_casos');
        $writer->save($filename);

        return $this->file($filename, 'exportar_casos.xlsx');
    }
}
