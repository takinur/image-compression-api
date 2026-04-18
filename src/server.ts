import express, { Request, Response } from 'express';
import Stripe from 'stripe';
import nodemailer from 'nodemailer';
import multer, { FileFilterCallback } from 'multer';
import path from 'path';
import fs from 'fs';
import dotenv from 'dotenv';

dotenv.config();

const MIN_FILES = 18;
const MAX_FILES = 25;
const MAX_FILE_SIZE_BYTES = 4 * 1024 * 1024; // 4MB per file

const app = express();
const PORT = process.env.PORT ?? 3000;

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY ?? '');

const upload = multer({
  storage: multer.memoryStorage(),
  limits: { fileSize: MAX_FILE_SIZE_BYTES },
  fileFilter: (
    _req: Request,
    file: Express.Multer.File,
    cb: FileFilterCallback
  ) => {
    const allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    cb(null, allowed.includes(file.mimetype));
  },
});

app.use(express.json());
app.use(express.static(path.join(__dirname, '..', 'public')));

// GET /upload — verify Stripe session then serve the upload form
app.get('/upload', async (req: Request, res: Response): Promise<void> => {
  const { session_id } = req.query;

  if (!session_id || typeof session_id !== 'string') {
    res.status(400).send('Missing session_id parameter.');
    return;
  }

  try {
    await stripe.checkout.sessions.retrieve(session_id);
    const html = fs.readFileSync(
      path.join(__dirname, '..', 'views', 'upload.html'),
      'utf-8'
    );
    res.send(html);
  } catch {
    res.status(400).send('Invalid or expired session.');
  }
});

// POST /api/upload — receive compressed images, verify session, send email
app.post(
  '/api/upload',
  upload.array('files', MAX_FILES),
  async (req: Request, res: Response): Promise<void> => {
    const sessionId = req.query.id as string;
    const files = req.files as Express.Multer.File[] | undefined;

    if (!files?.length) {
      res.status(400).json({ error: 'No files provided.' });
      return;
    }

    if (files.length < MIN_FILES || files.length > MAX_FILES) {
      res.status(400).json({
        error: `Upload between ${MIN_FILES} and ${MAX_FILES} images.`,
      });
      return;
    }

    try {
      const session = await stripe.checkout.sessions.retrieve(sessionId);
      const customerEmail = session.customer_details?.email;

      if (!customerEmail) {
        res.status(400).json({ error: 'Customer email not found.' });
        return;
      }

      await sendConfirmationEmail(customerEmail);
      res.json({ success: true });
    } catch (err) {
      console.error('Upload error:', err);
      res.status(500).json({ error: 'Upload failed. Please try again.' });
    }
  }
);

function getEmailTemplate(): string {
  return `
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <style>
        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body {
          background: linear-gradient(123.53deg, #0B3AB0 0%, #510A7A 100%);
          min-height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
          padding: 20px;
        }
        .card {
          background: white;
          border-radius: 10px;
          width: 100%;
          max-width: 550px;
          padding: 40px;
          text-align: center;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
        }
        p { padding-bottom: 15px; font-size: 16px; line-height: 1.6; }
        @media (max-width: 600px) { p { font-size: 14px; } }
      </style>
    </head>
    <body>
      <div class="card">
        <h1>Hi there!</h1>
        <br />
        <p>We've received your order for a Digital You AI avatar. Our AI nanobots are already on it — expect your avatar in your inbox within a few hours.</p>
        <p>Thank you for choosing Digital You. We can't wait for you to see it!</p>
        <p>Welcome to the AI world,<br /><strong>Digital You Team</strong></p>
        <br />
        <h3>Important</h3>
        <p>No email within a few hours? Check your Promotions or Spam folder. Still nothing after 24 hours? Reach out and we'll get it sorted.</p>
      </div>
    </body>
    </html>
  `;
}

async function sendConfirmationEmail(to: string): Promise<void> {
  const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: Number(process.env.SMTP_PORT ?? 587),
    secure: false,
    auth: {
      user: process.env.SMTP_USER,
      pass: process.env.SMTP_PASS,
    },
  });

  await transporter.sendMail({
    from: `"Digital You" <${process.env.SMTP_FROM ?? 'noreply@example.com'}>`,
    to,
    subject: 'Digital You — Your Order is Confirmed',
    html: getEmailTemplate(),
  });
}

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});

export default app;
