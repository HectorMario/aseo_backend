<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentAssignment;
use App\Models\Assignee;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::with('client')->get();

        return response()->json($appointments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $appointment = Appointment::create($request->validated());



        $appointmentAssignments = [];

        foreach ($request['assignee_id'] as $assigneeId) {
            // Buscar el id del assignee en la tabla Asseignee
            $assignee = Assignee::where('name', $assigneeId)->first();

            if ($assignee) {
                // Crear el AppointmentAssignment con el assignee_id correspondiente
                $appointmentAssignment = AppointmentAssignment::create([
                    'assignee_id' => $assignee->id,
                    'appointment_id' => $appointment->id
                ]);

                $appointmentAssignments[] = $appointmentAssignment;
            }
        }

        return response()->json(['message' => 'Asignación de cita creada exitosamente', 'data' => $appointmentAssignments], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        // Validar la solicitud
        $validatedData = $request->validated();

        // Actualizar la cita
        $appointment->update($validatedData);

        AppointmentAssignment::where('appointment_id', $appointment->id)->delete();

        $appointmentAssignments = [];

        foreach ($request['assignee_id'] as $assigneeId) {
            // Buscar el id del assignee en la tabla Asseignee
            $assignee = Assignee::where('name', $assigneeId)->first();

            if ($assignee) {
                // Crear el AppointmentAssignment con el assignee_id correspondiente
                $appointmentAssignment = AppointmentAssignment::create([
                    'assignee_id' => $assignee->id,
                    'appointment_id' => $appointment->id
                ]);

                $appointmentAssignments[] = $appointmentAssignment;
            }
        }


        return response()->json(['message' => 'Cita actualizada exitosamente'], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
