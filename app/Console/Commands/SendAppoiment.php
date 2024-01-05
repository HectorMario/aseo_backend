<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendAppoiment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appoiment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtiene el mensaje a enviar
        $message = $this->getMessage();

        // Obtiene la configuración de Telegram
        $telegramBotToken = $this->getTelegramBotToken();
        $chatId = $this->getChatId();

        // Envía el mensaje
        $this->sendMessage($telegramBotToken, $chatId, $message);
    }

    /**
     * Obtener el mensaje a enviar.
     */
    private function getMessage()
    {
        // Consulta SQL
        $result = DB::select('SELECT a.id AS id_appointment, a.appointment_date, a.appointment_time, 
        GROUP_CONCAT(s.name) AS name_assignees, 
        GROUP_CONCAT(s.email) AS email_assignees, 
        c.first_name AS first_name_client, 
        c.last_name AS last_name_client, 
        c.phone_number AS phone_client, 
        c.address AS address_client
    FROM `appointments` AS a
    INNER JOIN appointment_assignments AS aa ON aa.appointment_id = a.id
    INNER JOIN assignees AS s ON s.id = aa.assignee_id
    INNER JOIN clients AS c ON c.id = a.client_id
    WHERE a.appointment_date = DATE(DATE_ADD(NOW(), INTERVAL 1 DAY))
    GROUP BY a.id, a.appointment_date, a.appointment_time, c.first_name, c.last_name, c.phone_number, c.address;
    ');

        // Decodificar el JSON
        $resultArray = json_decode(json_encode($result), true);

        // Verificar si hay citas para el día siguiente
        if (count($resultArray) > 0) {
            $message = "¡Aviso de citas para mañana!\n\n";

            // Crear un array asociativo para agrupar asignaciones por id_appointment
            $groupedAppointments = [];

            foreach ($resultArray as $appointment) {
                $idAppointment = $appointment['id_appointment'];

                // Verificar si ya existe el id_appointment en el array agrupado
                if (!isset($groupedAppointments[$idAppointment])) {
                    $groupedAppointments[$idAppointment] = [];
                }

                // Agregar la asignación al grupo
                $groupedAppointments[$idAppointment][] = $appointment;
            }

            // Construir el mensaje con los grupos de asignaciones
            foreach ($groupedAppointments as $appointmentsGroup) {
                foreach ($appointmentsGroup as $appointment) {
                    $message .= "Cliente: {$appointment['first_name_client']} {$appointment['last_name_client']}\n";
                    $message .= "Fecha de cita: {$appointment['appointment_date']} a las {$appointment['appointment_time']}\n";
                    $message .= "Asignado a: {$appointment['name_assignees']}\n";
                    $message .= "Teléfono del cliente: {$appointment['phone_client']}\n";
                    $message .= "Dirección del cliente: {$appointment['address_client']}\n\n";
                }

                // Agregar un espacio entre grupos
                $message .= "\n";
            }
        } else {
            $message = "¡Buenas noticias! 😄 Mañana es un día libre para las citas. Descansen y disfruten de su tiempo libre. No hay citas programadas para mañana.";
        }

        // Puedes personalizar el mensaje según tus necesidades
        return $message;
    }



    /**
     * Obtener el token del bot de Telegram.
     */
    private function getTelegramBotToken()
    {
        return '6417296850:AAGBydjU9dc-BK9dEXUbZXjZozghsKxQrAc'; // Reemplaza con tu token
    }

    /**
     * Obtener el chat ID.
     */
    private function getChatId()
    {
        return '-1002096118418'; // Reemplaza con tu chat ID
    }

    /**
     * Enviar un mensaje a través de la API de Telegram.
     */
    private function sendMessage($token, $chatId, $message)
    {
        // URL de la API de Telegram para enviar mensajes
        $telegramApiUrl = "https://api.telegram.org/bot{$token}/sendMessage";

        // Configurar el cliente Guzzle
        $client = new Client();

        // Enviar el mensaje
        $response = $client->post($telegramApiUrl, [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $message,
            ],
        ]);

        $this->info('Mensaje enviado exitosamente.');
    }
}
